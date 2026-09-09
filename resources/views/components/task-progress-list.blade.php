@props([
    'progress' => null,
    'selected' => null,
])

@php
    use App\Support\Percent;

    $has = $progress !== null && $selected !== null;
    $tasks = $has ? collect($progress['tasks']) : collect();
    $shown = $tasks->take(5);
@endphp

<div class="table-container">
    <div class="card-header">
        <div>
            <div class="card-title">Kegiatan / Task Terbaru</div>
            <div style="font-size:12px; color:#94A3B8; margin-top:2px;">Progres tiap kegiatan (berbobot)</div>
        </div>
        @if ($has)
            <a href="{{ route('magang.task.index', $selected) }}" class="btn btn-ghost btn-sm" style="font-size:12px;">
                Lihat Semua Kegiatan →
            </a>
        @endif
    </div>

    @if ($has && $tasks->isNotEmpty())
        @foreach ($shown as $task)
            <a href="{{ route('magang.task.show', [$selected, $task]) }}"
               style="display:flex; align-items:center; gap:14px; padding:14px 20px; text-decoration:none; border-bottom:1px solid #F1F5F9; transition: background 120ms;"
               onmouseover="this.style.background='#F8FAFC'" onmouseout="this.style.background='transparent'">
                <div style="flex:1; min-width:0;">
                    <div style="display:flex; align-items:center; justify-content:space-between; gap:12px; margin-bottom:8px;">
                        <div style="font-size:13.5px; font-weight:600; color:#0F172A; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ $task->nama_kegiatan }}</div>
                        <div style="display:flex; align-items:center; gap:8px; flex-shrink:0;">
                            <span style="font-size:11.5px; color:#94A3B8;">{{ $task->bobot }}%</span>
                            <span class="badge {{ $task->status === 'selesai' ? 'badge-success' : ($task->status === 'berjalan' ? 'badge-info' : 'badge-gray') }}">{{ $task->status_label }}</span>
                        </div>
                    </div>
                    <div class="progress-bar" style="width:100%;">
                        <div class="progress-fill" style="width:{{ min($task->progress, 100) }}%; background:{{ $task->progress >= 100 ? '#16A34A' : ($task->progress > 0 ? '#2563EB' : '#CBD5E1') }};"></div>
                    </div>
                </div>
                <div style="font-size:16px; font-weight:700; color:{{ $task->progress >= 100 ? '#16A34A' : '#2563EB' }}; flex-shrink:0; min-width:52px; text-align:right;">
                    {{ Percent::format($task->progress) }}%
                </div>
            </a>
        @endforeach

        <div style="padding:13px 20px; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:8px;">
            <div style="font-size:12.5px; color:#64748B;">
                Kontribusi bobot: <span style="font-weight:600; color:#0F172A;">{{ Percent::format($progress['total_bobot']) }}%</span>
                @if (!$progress['bobot_penuh'])
                    <span style="color:#D97706;">(belum mencapai 100%)</span>
                @endif
            </div>
            <a href="{{ route('progress') }}" class="btn btn-secondary btn-sm" style="font-size:12px;">
                Halaman Progress →
            </a>
        </div>
    @else
        <div class="empty-state">
            <div class="empty-state-icon">
                <svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <div class="empty-state-title">Belum ada kegiatan/task</div>
            <div class="empty-state-desc">Buat kegiatan sebagai induk logbook untuk menghitung progress berbobot.</div>
            @if ($has)
                <a href="{{ route('magang.task.create', $selected) }}" class="btn btn-primary">Tambah Kegiatan</a>
            @endif
        </div>
    @endif
</div>