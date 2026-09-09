@props(['progress' => null])

@php
    $hasProgress = $progress !== null;
    $series = $hasProgress ? $progress['series'] : [];
    $target = $hasProgress ? $progress['target_days'] : 0;
    $completed = $hasProgress ? $progress['completed_days'] : 0;
    $empty = ! $hasProgress || $completed === 0;

    $labels = array_map(fn ($s) => $s['minggu'], $series);
    $values = array_map(fn ($s) => $s['persen'], $series);
    $activities = array_map(fn ($s) => $s['aktivitas'], $series);
@endphp

<div class="table-container h-full flex flex-col">
    <div class="card-header">
        <div>
            <div class="card-title">Perkembangan Progres Logbook</div>
            <div style="font-size:12px; color:#94A3B8; margin-top:2px;">Progres kumulatif dari minggu ke minggu</div>
        </div>
    </div>
    <div class="card-body flex-1">
        <div style="position:relative; height:280px;">
            <canvas id="logbook-progress-chart" aria-label="Grafik perkembangan progres logbook" role="img"></canvas>
            @if ($empty)
                <div style="position:absolute; inset:0; display:flex; align-items:center; justify-content:center; pointer-events:none;">
                    <div style="text-align:center; max-width:300px; padding:16px;">
                        <div style="width:44px; height:44px; border-radius:12px; background:#F1F5F9; display:flex; align-items:center; justify-content:center; margin:0 auto 12px; color:#94A3B8;">
                            <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 13v-1m4 1v-3m4 3V8M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div style="font-size:13.5px; font-weight:600; color:#374151;">Belum ada aktivitas logbook</div>
                        <div style="font-size:12.5px; color:#94A3B8; margin-top:4px; line-height:1.5;">
                            Mulai isi logbook untuk melihat perkembangan progres.
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
            const el = document.getElementById('logbook-progress-chart');
            if (!el || typeof window.Chart === 'undefined') return;

            const series = @json($series);
            const labels = series.map((s) => s.minggu);
            const values = series.map((s) => s.persen);
            const activities = series.map((s) => s.aktivitas);
            const target = @json($target);

            new Chart(el, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Progres',
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
                        fill: false,
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
                                label: (ctx) => [
                                    'Progress: ' + ctx.parsed.y + '%',
                                    'Aktivitas: ' + activities[ctx.dataIndex] + ' / ' + target + ' hari',
                                ],
                            },
                        },
                    },
                    scales: {
                        y: {
                            min: 0,
                            max: 100,
                            ticks: { stepSize: 20, color: '#64748B', font: { size: 11 } },
                            grid: { color: '#F1F5F9' },
                            border: { display: false },
                            title: { display: false },
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