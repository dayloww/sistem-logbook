<?php

namespace App\Http\Controllers;

use App\Models\Logbook;
use App\Models\Magang;
use App\Services\TaskProgressService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
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

        $tasks = $magang->tasks()->orderBy('nama_kegiatan')->get();
        $selectedTaskId = $request->integer('task_id') ?: null;

        return view('logbook.create', compact('magang', 'defaultDate', 'tasks', 'selectedTaskId'));
    }

    public function store(Request $request, Magang $magang)
    {
        $validated = $this->validated($request, $magang);

        $task = $magang->tasks()->findOrFail($validated['task_id']);

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
        $tasks = $magang->tasks()->orderBy('nama_kegiatan')->get();

        return view('logbook.edit', compact('logbook', 'magang', 'tasks'));
    }

    public function update(Request $request, Logbook $logbook)
    {
        $validated = $this->validated($request, $logbook->magang);

        $validated['ttd_confirmed'] = $request->boolean('ttd_confirmed');

        $this->preventTaskRegression($request, $logbook, $validated);

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