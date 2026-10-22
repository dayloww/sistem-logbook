@props(['href' => null, 'icon' => 'chart', 'accent' => 'blue', 'label', 'value', 'caption'])

@php
    $icons = [
        'users' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>',
        'briefcase' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>',
        'check' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>',
        'doc' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>',
    ];
    $svg = $icons[$icon] ?? $icons['chart'];
@endphp

<div class="dash-metric {{ $href ? 'is-link' : '' }}">
    @if ($href)
        <a href="{{ $href }}" class="dash-metric-inner" aria-label="{{ $label }}: {{ $value }}">
    @else
        <div class="dash-metric-inner" aria-label="{{ $label }}: {{ $value }}">
    @endif
        <span class="dash-metric-icon dash-accent-{{ $accent }}" aria-hidden="true">
            <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor">{!! $svg !!}</svg>
        </span>
        <span class="dash-metric-data">
            <span class="dash-metric-label">{{ $label }}</span>
            <strong class="dash-metric-value">{{ $value }}</strong>
            <small class="dash-metric-caption">{{ $caption }}</small>
        </span>
    @if ($href)
        </a>
    @else
        </div>
    @endif
</div>