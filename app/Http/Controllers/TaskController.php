<?php

namespace App\Http\Controllers;

use App\Models\Magang;
use App\Models\Task;
use App\Services\TaskProgressService;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index(Magang $magang)
    {
        $tasks = $magang->tasks()
            ->withCount('logbooks')
            ->orderByRaw('COALESCE(tanggal_mulai, "9999-12-31")')
            ->orderBy('nama_kegiatan')
            ->get();

        $progress = app(TaskProgressService::class)->overview($magang);

        return view('task.index', compact('magang', 'tasks', 'progress'));
    }

    public function create(Magang $magang)
    {
        $task = null;

        return view('task.form', compact('magang', 'task'));
    }

    public function store(Request $request, Magang $magang)
    {
        $validated = $this->validated($request, $magang);
        $validated['magang_id'] = $magang->id;

        Task::create($validated);

        return redirect()->route('magang.task.index', $magang)->with('success', 'Kegiatan/task berhasil ditambahkan.');
    }

    public function show(Magang $magang, Task $task)
    {
        $magang = $task->magang;
        $logbooks = $task->logbooks()->orderBy('tanggal')->get();

        return view('task.show', compact('magang', 'task', 'logbooks'));
    }

    public function edit(Magang $magang, Task $task)
    {
        $magang = $task->magang;

        return view('task.form', compact('magang', 'task'));
    }

    public function update(Request $request, Magang $magang, Task $task)
    {
        $validated = $this->validated($request, $task->magang, $task);

        $task->update($validated);

        return redirect()->route('magang.task.show', [$task->magang, $task])->with('success', 'Kegiatan/task berhasil diperbarui.');
    }

    public function destroy(Magang $magang, Task $task)
    {
        $magangId = $task->magang_id;
        $task->delete();

        app(TaskProgressService::class)->recalculateAll(Magang::findOrFail($magangId));

        return redirect()->route('magang.task.index', $magangId)->with('success', 'Kegiatan/task berhasil dihapus.');
    }

    protected function validated(Request $request, Magang $magang, ?Task $ignore = null): array
    {
        $start = $magang->tanggal_mulai->format('Y-m-d');
        $end = $magang->tanggal_selesai->format('Y-m-d');

        return $request->validate([
            'nama_kegiatan' => ['required', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string'],
            'bobot' => [
                'required', 'integer', 'min:0', 'max:100',
                function ($attribute, $value, $fail) use ($magang, $ignore) {
                    $current = Task::where('magang_id', $magang->id)
                        ->when($ignore, fn ($q) => $q->where('id', '!=', $ignore->id))
                        ->sum('bobot');

                    if ($current + (int) $value > 100) {
                        $fail('Total bobot seluruh kegiatan tidak boleh melebihi 100%.');
                    }
                },
            ],
            'tanggal_mulai' => ['nullable', 'date', 'after_or_equal:'.$start, 'before_or_equal:'.$end],
            'tanggal_selesai' => ['nullable', 'date', 'after_or_equal:'.$start, 'before_or_equal:'.$end, 'after_or_equal:tanggal_mulai'],
        ]);
    }
}