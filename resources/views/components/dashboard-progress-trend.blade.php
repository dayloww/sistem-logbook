@props(['trend' => [], 'target' => 0])

@php
    $points = collect($trend)->values()->all();
    $empty = empty($points);
@endphp

<div class="trend-panel">
    <div class="trend-chart-wrap">
        <canvas id="dashboard-progress-trend" aria-label="Grafik tren progres logbook" role="img"></canvas>
        @if ($empty)
            <div class="trend-empty">
                <div class="trend-empty-icon" aria-hidden="true">
                    <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                </div>
                <strong>Belum ada aktivitas logbook</strong>
                <span>Mulai isi logbook untuk melihat perkembangan progres.</span>
            </div>
        @endif
    </div>
</div>

@push('scripts')
    <script defer src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const el = document.getElementById('dashboard-progress-trend');
            if (!el || typeof window.Chart === 'undefined') return;

            const series = @json($points);
            if (series.length === 0) return;

            new Chart(el, {
                type: 'line',
                data: {
                    labels: series.map((s) => s.label),
                    datasets: [{
                        label: 'Progress',
                        data: series.map((s) => s.persen),
                        borderColor: '#2563EB',
                        backgroundColor: (context) => {
                            const { ctx, chartArea } = context.chart;
                            if (!chartArea) return 'rgba(37,99,235,0)';
                            const gradient = ctx.createLinearGradient(0, chartArea.top, 0, chartArea.bottom);
                            gradient.addColorStop(0, 'rgba(37,99,235,0.20)');
                            gradient.addColorStop(1, 'rgba(37,99,235,0.01)');
                            return gradient;
                        },
                        fill: true,
                        tension: 0.4,
                        borderWidth: 2.5,
                        pointRadius: 4.5,
                        pointHoverRadius: 7,
                        pointBackgroundColor: '#FFFFFF',
                        pointBorderColor: '#2563EB',
                        pointBorderWidth: 2.5,
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: { mode: 'index', intersect: false },
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
                                title: (items) => items[0] ? series[items[0].dataIndex].full : '',
                                label: (ctx) => [
                                    'Progress: ' + ctx.parsed.y + '%',
                                    'Aktivitas: ' + series[ctx.dataIndex].aktivitas + ' hari',
                                    'Periode: ' + series[ctx.dataIndex].minggu,
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
                        },
                        x: {
                            ticks: { color: '#64748B', font: { size: 11 }, maxRotation: 0 },
                            grid: { display: false },
                            border: { display: false },
                        },
                    },
                    animation: { duration: 650, easing: 'easeOutQuart' },
                },
            });
        });
    </script>
@endpush