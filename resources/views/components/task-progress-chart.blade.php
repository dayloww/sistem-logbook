@props(['progress' => null])

@php
    use App\Support\Percent;

    $has = $progress !== null;
    $weekly = $has ? collect($progress['weekly']) : collect();
    $labels = $weekly->map(fn ($w) => 'Minggu '.$w->minggu_ke)->values();
    $values = $weekly->map(fn ($w) => round((float) $w->progress_akhir, 1))->values();
    $perkembangan = $weekly->map(fn ($w) => round((float) $w->perkembangan, 1))->values();
    $empty = ! $has || $weekly->isEmpty() || max($values->all() ?: [0]) === 0;
@endphp

<div class="table-container h-full flex flex-col">
    <div class="card-header">
        <div>
            <div class="card-title">Grafik Progress Mingguan</div>
            <div style="font-size:12px; color:#94A3B8; margin-top:2px;">Posisi progress keseluruhan di akhir tiap minggu</div>
        </div>
    </div>
    <div class="card-body flex-1">
        <div style="position:relative; height:280px;">
            <canvas id="task-progress-chart" aria-label="Grafik progress mingguan" role="img"></canvas>
            @if ($empty)
                <div style="position:absolute; inset:0; display:flex; align-items:center; justify-content:center; pointer-events:none;">
                    <div style="text-align:center; max-width:300px; padding:16px;">
                        <div style="width:44px; height:44px; border-radius:12px; background:#F1F5F9; display:flex; align-items:center; justify-content:center; margin:0 auto 12px; color:#94A3B8;">
                            <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 13v-1m4 1v-3m4 3V8M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div style="font-size:13.5px; font-weight:600; color:#374151;">Belum ada progress task</div>
                        <div style="font-size:12.5px; color:#94A3B8; margin-top:4px; line-height:1.5;">
                            Tambahkan kegiatan/task dan isi logbook untuk melihat grafik.
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
    <script defer src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const el = document.getElementById('task-progress-chart');
            if (!el || typeof window.Chart === 'undefined') return;

            const labels = @json($labels);
            const values = @json($values);
            const perkembangan = @json($perkembangan);

            new Chart(el, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Progress keseluruhan',
                        data: values,
                        borderColor: '#2563EB',
                        backgroundColor: '#2563EB',
                        borderWidth: 2.5,
                        tension: 0.35,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        pointBackgroundColor: '#2563EB',
                        pointBorderColor: '#FFFFFF',
                        pointBorderWidth: 2,
                        fill: true,
                        backgroundColor: 'rgba(37, 99, 235, 0.08)',
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#0F172A',
                            titleColor: '#E2E8F0',
                            bodyColor: '#F8FAFC',
                            padding: 12,
                            cornerRadius: 10,
                            displayColors: false,
                            callbacks: {
                                title: (items) => items[0] ? items[0].label : '',
                                label: (ctx) => {
                                    const pct = ctx.parsed.y.toLocaleString('id-ID');
                                    const delta = perkembangan[ctx.dataIndex];
                                    const deltaStr = (delta > 0 ? '+' : '') + delta.toLocaleString('id-ID');
                                    return ['Progress: ' + pct + '%', 'Perkembangan: ' + deltaStr + '%'];
                                },
                            },
                        },
                    },
                    scales: {
                        y: {
                            min: 0,
                            max: 100,
                            ticks: { stepSize: 20, color: '#64748B', font: { size: 11 }, callback: (v) => v + '%' },
                            grid: { color: '#F1F5F9' },
                            border: { display: false },
                        },
                        x: {
                            ticks: { color: '#64748B', font: { size: 11 } },
                            grid: { display: false },
                            border: { display: false },
                        },
                    },
                    animation: { duration: 600 },
                },
            });
        });
    </script>
@endpush