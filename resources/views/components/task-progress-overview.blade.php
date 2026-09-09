@props([
    'progress' => null,
    'magangs' => null,
    'selected' => null,
])

@php
    use App\Support\Percent;

    $has = $progress !== null;
    $overall = $has ? (float) $progress['overall'] : 0;
    $currentWeek = $has ? $progress['current_week_progress'] : 0;
    $perkembangan = $has ? $progress['perkembangan_minggu_ini'] : 0;
    $total = $has ? $progress['total_tasks'] : 0;
    $selesai = $has ? $progress['selesai'] : 0;
    $berjalan = $has ? $progress['berjalan'] : 0;
    $belum = $has ? $progress['belum'] : 0;
    $display = Percent::format($overall);
@endphp

<div class="table-container h-full flex flex-col">
    <div class="card-header">
        <div>
            <div class="card-title">Progress Magang</div>
            <div style="font-size:12px; color:#94A3B8; margin-top:2px;">
                {{ $has && $selected ? $selected->mahasiswa->nama.' · '.$selected->instansi : 'Belum ada data magang' }}
            </div>
        </div>
        @if ($has && $magangs && $magangs->count() > 1)
            <form method="GET" action="{{ route('progress') }}">
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
        <div class="progress-ring" data-percent="{{ $overall }}">
            <svg width="180" height="180" viewBox="0 0 180 180" role="img" aria-label="Progress keseluruhan {{ $display }} persen">
                <circle cx="90" cy="90" r="76" fill="none" stroke="#E2E8F0" stroke-width="13" />
                <circle
                    class="progress-ring-fill"
                    cx="90" cy="90" r="76" fill="none"
                    stroke="#2563EB" stroke-width="13" stroke-linecap="round"
                    stroke-dasharray="477.522" stroke-dashoffset="477.522"
                    transform="rotate(-90 90 90)"
                />
            </svg>
            <div class="ring-center">
                <div style="font-size:34px; font-weight:700; color:#0F172A; line-height:1.1;">{{ $display }}%</div>
                <div style="font-size:12.5px; color:#64748B; margin-top:2px;">Progress keseluruhan</div>
            </div>
        </div>

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:8px; width:100%; max-width:300px;">
            <div style="padding:10px 12px; text-align:center; background:#F8FAFC; border:1px solid #E2E8F0; border-radius:10px;">
                <div style="font-size:15px; font-weight:700; color:#2563EB; line-height:1.2;">{{ Percent::format($currentWeek) }}%</div>
                <div style="font-size:11px; color:#64748B; margin-top:2px;">Minggu berjalan</div>
            </div>
            <div style="padding:10px 12px; text-align:center; background:#F8FAFC; border:1px solid #E2E8F0; border-radius:10px;">
                <div style="font-size:15px; font-weight:700; color:{{ $perkembangan < 0 ? '#DC2626' : '#16A34A' }}; line-height:1.2;">{{ Percent::signed($perkembangan) }}%</div>
                <div style="font-size:11px; color:#64748B; margin-top:2px;">Perkembangan minggu ini</div>
            </div>
        </div>

        @if (!$has)
            <div style="text-align:center; max-width:260px;">
                <div style="font-size:13.5px; font-weight:600; color:#374151;">Belum ada data magang</div>
            </div>
        @endif
    </div>

    <div style="display:grid; grid-template-columns:repeat(4, 1fr); border-top:1px solid #F1F5F9;">
        @php
            $stats = [
                ['label' => 'Total Task', 'value' => number_format($total)],
                ['label' => 'Selesai', 'value' => number_format($selesai)],
                ['label' => 'Berjalan', 'value' => number_format($berjalan)],
                ['label' => 'Belum Mulai', 'value' => number_format($belum)],
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
    .progress-ring { position: relative; width: 180px; height: 180px; }
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
            const targetOffset = circumference * (1 - Math.min(percent, 100) / 100);

            requestAnimationFrame(() => {
                fill.style.strokeDashoffset = targetOffset;
            });
        });
    });
</script>