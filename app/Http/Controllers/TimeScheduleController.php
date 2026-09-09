<?php

namespace App\Http\Controllers;

use App\Models\Magang;
use App\Models\Task;
use App\Models\TimeSchedule;
use App\Services\TaskProgressService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TimeScheduleController extends Controller
{
    public function index(Request $request, Magang $magang)
    {
        $jumlahMinggu = $magang->jumlahMinggu();

        $schedules = $magang->timeSchedules()
            ->with('task')
            ->when($request->filled('cari'), fn ($q) => $q->where('kegiatan', 'like', '%'.$request->string('cari').'%'))
            ->when($request->filled('minggu'), fn ($q) => $q->where('minggu_ke', $request->integer('minggu')))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->when($request->filled('prioritas'), fn ($q) => $q->where('prioritas', $request->string('prioritas')))
            ->when($request->filled('dari'), fn ($q) => $q->whereDate('tanggal_mulai', '>=', $request->date('dari')))
            ->when($request->filled('sampai'), fn ($q) => $q->whereDate('tanggal_selesai', '<=', $request->date('sampai')))
            ->orderBy('tanggal_mulai')
            ->get();

        $filters = $request->only(['cari', 'minggu', 'status', 'prioritas', 'dari', 'sampai']);

        return view('time-schedule.index', compact('magang', 'schedules', 'jumlahMinggu', 'filters'));
    }

    public function create(Magang $magang)
    {
        $timeSchedule = null;

        return view('time-schedule.form', compact('magang', 'timeSchedule'));
    }

    public function store(Request $request, Magang $magang)
    {
        $validated = $this->validated($request, $magang);
        $validated['magang_id'] = $magang->id;
        $validated['minggu_ke'] = $magang->mingguKe($validated['tanggal_mulai']);

        $task = Task::create([
            'magang_id' => $magang->id,
            'nama_kegiatan' => $validated['kegiatan'],
            'deskripsi' => $validated['deskripsi'] ?? null,
            'bobot' => 0,
            'progress' => 0,
            'status' => 'belum_dimulai',
            'tanggal_mulai' => $validated['tanggal_mulai'],
            'tanggal_selesai' => $validated['tanggal_selesai'],
        ]);

        $validated['task_id'] = $task->id;

        $timeSchedule = TimeSchedule::create($validated);

        return redirect()->route('magang.time-schedule.show', [$magang, $timeSchedule])->with('success', 'Time schedule berhasil ditambahkan.');
    }

    public function show(Magang $magang, TimeSchedule $timeSchedule)
    {
        $magang = $timeSchedule->magang;
        $logbooks = $timeSchedule->task?->logbooks()->orderBy('tanggal')->get() ?? collect();

        return view('time-schedule.show', compact('magang', 'timeSchedule', 'logbooks'));
    }

    public function edit(Magang $magang, TimeSchedule $timeSchedule)
    {
        $magang = $timeSchedule->magang;

        return view('time-schedule.form', compact('magang', 'timeSchedule'));
    }

    public function update(Request $request, Magang $magang, TimeSchedule $timeSchedule)
    {
        $validated = $this->validated($request, $timeSchedule->magang);
        $validated['minggu_ke'] = $timeSchedule->magang->mingguKe($validated['tanggal_mulai']);

        $task = $timeSchedule->task;

        if ($task) {
            $task->update([
                'nama_kegiatan' => $validated['kegiatan'],
                'deskripsi' => $validated['deskripsi'] ?? null,
                'tanggal_mulai' => $validated['tanggal_mulai'],
                'tanggal_selesai' => $validated['tanggal_selesai'],
            ]);
        }

        $timeSchedule->update($validated);

        return redirect()->route('magang.time-schedule.show', [$timeSchedule->magang, $timeSchedule])->with('success', 'Time schedule berhasil diperbarui.');
    }

    public function destroy(Magang $magang, TimeSchedule $timeSchedule)
    {
        $magangId = $timeSchedule->magang_id;
        $task = $timeSchedule->task;
        $magang = $timeSchedule->magang;

        $timeSchedule->delete();

        if ($task && $task->logbooks()->doesntExist()) {
            $task->delete();

            app(TaskProgressService::class)->syncAfterLogbookChange($magang);
        }

        return redirect()->route('magang.time-schedule.index', $magangId)->with('success', 'Time schedule berhasil dihapus.');
    }

    public function available(Request $request, Magang $magang)
    {
        $validated = $request->validate([
            'tanggal' => ['required', 'date'],
        ]);

        $tanggal = Carbon::parse($validated['tanggal']);

        $tasks = $magang->tasks()
            ->forLogbookDate($tanggal->toDateString())
            ->with('timeSchedule')
            ->orderBy('nama_kegiatan')
            ->get();

        return response()->json($tasks->map(fn (Task $task) => [
            'task_id' => $task->id,
            'nama_kegiatan' => $task->nama_kegiatan,
            'progress' => $task->progress,
            'target' => $task->timeSchedule?->target,
        ]));
    }

    protected function validated(Request $request, Magang $magang): array
    {
        $start = $magang->tanggal_mulai->format('Y-m-d');
        $end = $magang->tanggal_selesai->format('Y-m-d');

        return $request->validate([
            'kegiatan' => ['required', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string'],
            'target' => ['nullable', 'string'],
            'tanggal_mulai' => ['required', 'date', 'after_or_equal:'.$start, 'before_or_equal:'.$end],
            'tanggal_selesai' => ['required', 'date', 'after_or_equal:'.$start, 'before_or_equal:'.$end, 'after_or_equal:tanggal_mulai'],
            'prioritas' => ['required', Rule::in(['tinggi', 'sedang', 'rendah'])],
            'status' => ['required', Rule::in(['belum_dimulai', 'berjalan', 'selesai', 'ditunda'])],
            'catatan' => ['nullable', 'string'],
        ]);
    }
}
