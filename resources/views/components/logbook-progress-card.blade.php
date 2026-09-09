@props([
    'progress' => null,
    'magangs' => null,
    'selected' => null,
])

@php
    $hasProgress = $progress !== null;
    $pct = $hasProgress ? $progress['percent'] : 0;
    $completed = $hasProgress ? $progress['completed_days'] : 0;
    $target = $hasProgress ? $progress['target_days'] : 0;
    $remaining = $hasProgress ? $progress['remaining_days'] : 0;
    $status = $hasProgress ? $progress['status'] : ['key' => 'on_track', 'label' => 'On Track'];
    $empty = ! $hasProgress || $completed === 0;

    $badgeClass = match ($status['key']) {
        'perlu_perhatian' => 'badge-warning',
        'tertinggal' => 'badge-danger',
        default => 'badge-success',
    };
@endphp

<div class="table-container h-full flex flex-col">
    <div class="card-header">
        <div>
            <div class="card-title">Progress Logbook</div>
            <div style="font-size:12px; color:#94A3B8; margin-top:2px;">
                {{ $hasProgress && $selected ? $selected->mahasiswa->nama.' · '.$selected->instansi : 'Belum ada data magang' }}
            </div>
        </div>
        @if ($hasProgress && $magangs && $magangs->count() > 1)
            <form method="GET" action="{{ route('dashboard') }}">
                <select
                    name="magang"
                    onchange="this.form.submit()"
                    class="form-select"
                    style="width:auto; height:32px; padding:0 28px 0 10px; font-size:12px;"
                    aria-label="Pilih magang"
                >
                    @foreach ($magangs as $m)
                        <option value="{{ $m->id }}" @selected($selected && $selected->id === $m->id)>
                            {{ $m->mahasiswa->nama }} – {{ $m->instansi }}
                        </option>
                    @endforeach
                </select>
            </form>
        @endif
    </div>

    <div class="card-body flex-1 flex flex-col items-center justify-center gap-4">
        <div class="progress-ring" data-percent="{{ $pct }}">
            <svg width="170" height="170" viewBox="0 0 170 170" role="img" aria-label="Progres {{ $pct }} persen">
                <circle cx="85" cy="85" r="72" fill="none" stroke="#E2E8F0" stroke-width="12" />
                <circle
                    class="progress-ring-fill"
                    cx="85" cy="85" r="72" fill="none"
                    stroke="#2563EB" stroke-width="12" stroke-linecap="round"
                    stroke-dasharray="452.389" stroke-dashoffset="452.389"
                    transform="rotate(-90 85 85)"
                />
            </svg>
            <div class="ring-center">
                <div style="font-size:30px; font-weight:700; color:#0F172A; line-height:1.1;">{{ $pct }}%</div>
                <div style="font-size:12.5px; color:#64748B; margin-top:2px;">{{ $completed }} dari {{ $target }} hari</div>
            </div>
        </div>

        <div>
            <span class="badge {{ $badgeClass }}" style="font-size:12px; padding:4px 12px;">
                @if ($status['key'] === 'on_track')
                    <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="margin-right:5px;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                    </svg>
                @elseif ($status['key'] === 'perlu_perhatian')
                    <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="margin-right:5px;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
                    </svg>
                @else
                    <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="margin-right:5px;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                @endif
                {{ $status['label'] }}
            </span>
        </div>

        @if ($empty)
            <div style="text-align:center; max-width:260px;">
                <div style="font-size:13.5px; font-weight:600; color:#374151;">Belum ada aktivitas logbook</div>
                <div style="font-size:12.5px; color:#94A3B8; margin-top:4px; line-height:1.5;">
                    Mulai isi logbook untuk melihat perkembangan progres.
                </div>
            </div>
        @endif
    </div>

    <div style="display:grid; grid-template-columns:repeat(4, 1fr); border-top:1px solid #F1F5F9;">
        @php
            $stats = [
                ['label' => 'Total Aktivitas', 'value' => number_format($completed)],
                ['label' => 'Target Aktivitas', 'value' => number_format($target)],
                ['label' => 'Sisa Aktivitas', 'value' => number_format($remaining)],
                ['label' => 'Progress', 'value' => $pct.'%'],
            ];
        @endphp
        @foreach ($stats as $i => $stat)
            <div style="padding:14px 8px; text-align:center; {{ $i > 0 ? 'border-left:1px solid #F1F5F9;' : '' }}">
                <div style="font-size:20px; font-weight:700; color:#0F172A; line-height:1.2;">{{ $stat['value'] }}</div>
                <div style="font-size:11px; color:#94A3B8; margin-top:2px; font-weight:500;">{{ $stat['label'] }}</div>
            </div>
        @endforeach
    </div>
</div>

<style>
    .progress-ring { position: relative; width: 170px; height: 170px; }
    .progress-ring .ring-center {
        position: absolute; inset: 0;
        display: flex; flex-direction: column; align-items: center; justify-content: center;
    }
    .progress-ring-fill { transition: stroke-dashoffset 600ms ease-out; }
</style>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.progress-ring').forEach((ring) => {
            const fill = ring.querySelector('.progress-ring-fill');
            const percent = parseFloat(ring.dataset.percent) || 0;
            const circumference = parseFloat(fill.getAttribute('stroke-dasharray')) || 0;
            const targetOffset = circumference * (1 - percent / 100);

            requestAnimationFrame(() => {
                fill.style.strokeDashoffset = targetOffset;
            });
        });
    });
</script>