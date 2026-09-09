<?php

namespace App\Http\Controllers;

use App\Models\Logbook;
use App\Models\Magang;
use App\Models\Task;
use App\Services\TaskProgressService;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class LogbookController extends Controller
{
    public function index(Magang $magang)
    {
        $logbooks = $magang->logbooks()->with('task')->orderBy('tanggal', 'desc')->get();
        $jumlahMinggu = $magang->jumlahMinggu();

        $logbooksByWeek = collect(range(1, $jumlahMinggu))->map(fn ($week) => [
            'minggu_ke' => $week,
            'items' => $logbooks->filter(fn ($l) => $l->mingguKe() === $week)->values(),
        ])->filter(fn ($week) => $week['items']->isNotEmpty());

        return view('logbook.index', compact('magang', 'logbooksByWeek', 'jumlahMinggu', 'logbooks'));
    }

    public function create(Request $request, Magang $magang)
    {
        $lastDate = $magang->logbooks()->max('tanggal');
        $defaultDate = $lastDate ? Carbon::parse($lastDate)->addDay() : $magang->tanggal_mulai;

        $selectedTaskId = $request->integer('task_id') ?: null;
        $tasks = $this->tasksForDate($magang, $defaultDate, $selectedTaskId ? [$selectedTaskId] : []);

        return view('logbook.create', compact('magang', 'defaultDate', 'tasks', 'selectedTaskId'));
    }

    public function store(Request $request, Magang $magang)
    {
        $validated = $this->validated($request, $magang);

        $task = $magang->tasks()->findOrFail($validated['task_id']);

        $this->assertTaskActiveOnTanggal($task, $validated['tanggal']);

        $validated['magang_id'] = $magang->id;
        $validated['ttd_confirmed'] = $request->boolean('ttd_confirmed');

        $taskProgressBefore = (int) $task->progress;

        Logbook::create($validated);

        app(TaskProgressService::class)->syncAfterLogbookChange($magang);

        $message = 'Logbook berhasil disimpan.';

        if ((int) $validated['persentase_capaian'] < $taskProgressBefore) {
            return redirect()->route('magang.logbook.index', $magang)->with([
                'success' => $message,
                'warning' => "Progres kegiatan \"{$task->nama_kegiatan}\" tetap {$taskProgressBefore}% karena sistem memakai progres tertinggi (progres tidak bisa turun).",
            ]);
        }

        return redirect()->route('magang.logbook.index', $magang)->with('success', $message);
    }

    public function edit(Logbook $logbook)
    {
        $magang = $logbook->magang;
        $tasks = $this->tasksForDate($magang, $logbook->tanggal, [$logbook->task_id]);

        return view('logbook.edit', compact('logbook', 'magang', 'tasks'));
    }

    public function update(Request $request, Logbook $logbook)
    {
        $validated = $this->validated($request, $logbook->magang);

        $validated['ttd_confirmed'] = $request->boolean('ttd_confirmed');

        $this->preventTaskRegression($request, $logbook, $validated);

        $task = $logbook->magang->tasks()->findOrFail($validated['task_id']);
        $this->assertTaskActiveOnTanggal($task, $validated['tanggal']);

        $logbook->update($validated);

        app(TaskProgressService::class)->syncAfterLogbookChange($logbook->magang);

        return redirect()->route('magang.logbook.index', $logbook->magang_id)->with('success', 'Logbook berhasil diperbarui.');
    }

    public function destroy(Logbook $logbook)
    {
        $magangId = $logbook->magang_id;
        $magang = $logbook->magang;
        $logbook->delete();

        app(TaskProgressService::class)->syncAfterLogbookChange($magang);

        return redirect()->route('magang.logbook.index', $magangId)->with('success', 'Logbook berhasil dihapus.');
    }

    protected function validated(Request $request, Magang $magang): array
    {
        return $request->validate([
            'task_id' => ['required', 'integer', 'exists:tasks,id,magang_id,'.$magang->id],
            'tanggal' => [
                'required', 'date',
                'after_or_equal:'.$magang->tanggal_mulai->format('Y-m-d'),
                'before_or_equal:'.$magang->tanggal_selesai->format('Y-m-d'),
            ],
            'deskripsi_tugas' => ['required', 'string'],
            'target' => ['nullable', 'string'],
            'persentase_capaian' => ['required', 'integer', 'min:0', 'max:100'],
            'kendala' => ['nullable', 'string'],
            'ttd_confirmed' => ['nullable', 'in:0,1'],
        ]);
    }

    /**
     * Kumpulan task yang bisa dipilih pada Input Logbook untuk satu tanggal:
     * task yang time schedule-nya aktif pada tanggal tsb + task legacy tanpa jadwal.
     */
    protected function tasksForDate(Magang $magang, CarbonInterface $tanggal, array $extraTaskIds = []): Collection
    {
        return $magang->tasks()
            ->forLogbookDate($tanggal->toDateString())
            ->when($extraTaskIds, fn ($q) => $q->orWhereIn('id', $extraTaskIds))
            ->with('timeSchedule')
            ->orderBy('nama_kegiatan')
            ->get();
    }

    /**
     * Task yang berasal dari time schedule wajib aktif pada tanggal logbook.
     * Task legacy (tanpa jadwal) tetap diterima demi kompatibilitas.
     */
    protected function assertTaskActiveOnTanggal(Task $task, string $tanggal): void
    {
        $schedule = $task->timeSchedule;

        if (! $schedule) {
            return;
        }

        $tanggal = Carbon::parse($tanggal);

        if ($schedule->tanggal_mulai->lte($tanggal) && $schedule->tanggal_selesai->gte($tanggal)) {
            return;
        }

        $validator = Validator::make([], [
            'task_id' => ['required', Rule::in([0])],
        ], [
            'task_id.in' => 'Kegiatan "'.$task->nama_kegiatan.'" tidak aktif pada tanggal '.$tanggal->format('d/m/Y').' (jadwal: '.$schedule->tanggal_mulai->format('d/m/Y').' s/d '.$schedule->tanggal_selesai->format('d/m/Y').').',
        ]);

        throw new ValidationException($validator);
    }

    /**
     * Mencegah progress task mundur ketika mengedit logbook yang saat ini
     * memegang progres tertinggi.
     */
    protected function preventTaskRegression(Request $request, Logbook $logbook, array $validated): void
    {
        if (! $logbook->task_id || ! $logbook->task) {
            return;
        }

        $taskProgress = (int) $logbook->task->progress;
        $othersMax = (int) (Logbook::where('task_id', $logbook->task_id)
            ->where('id', '!=', $logbook->id)
            ->max('persentase_capaian') ?? 0);

        $suggested = (int) $validated['persentase_capaian'];

        if ($othersMax < $taskProgress && $suggested < $taskProgress) {
            $validator = Validator::make($validated, [
                'persentase_capaian' => ['required', 'integer', 'min:'.$taskProgress, 'max:100'],
            ], [
                'persentase_capaian.min' => "Progres kegiatan \"{$logbook->task->nama_kegiatan}\" tidak boleh diturunkan dari {$taskProgress}% (progres terbaik harus dipertahankan).",
            ]);

            throw new ValidationException($validator);
        }
    }
}
