<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Logbook Magang' }} - Sistem Logbook Magang</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body>

{{-- TOAST CONTAINER --}}
<div class="toast-container" id="toast-container">
    @if (session('success'))
        <div class="toast toast-success" role="alert">
            <span class="toast-icon">
                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </span>
            <span class="toast-msg">{{ session('success') }}</span>
            <button class="toast-close" onclick="this.closest('.toast').remove()" aria-label="Tutup">
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    @endif
    @if (session('error'))
        <div class="toast toast-error" role="alert">
            <span class="toast-icon">
                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </span>
            <span class="toast-msg">{{ session('error') }}</span>
            <button class="toast-close" onclick="this.closest('.toast').remove()" aria-label="Tutup">
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    @endif
    @if (session('warning'))
        <div class="toast toast-warning" role="alert" style="border-color:#FCD34D;">
            <span class="toast-icon">
                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="#D97706">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                </svg>
            </span>
            <span class="toast-msg" style="color:#92400E;">{{ session('warning') }}</span>
            <button class="toast-close" onclick="this.closest('.toast').remove()" aria-label="Tutup">
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    @endif
</div>

{{-- VALIDATION ERRORS (as toast) --}}
@if ($errors->any())
<div class="toast-container" style="top: {{ session('success') || session('error') || session('warning') ? '80px' : '20px' }}">
    <div class="toast toast-error" role="alert">
        <span class="toast-icon">
            <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </span>
        <div class="toast-msg">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
        <button class="toast-close" onclick="this.closest('.toast').remove()" aria-label="Tutup">
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>
</div>
@endif

{{-- SIDEBAR OVERLAY (mobile) --}}
<div class="sidebar-overlay" id="sidebar-overlay" onclick="closeSidebar()" aria-hidden="true"></div>

{{-- DELETE CONFIRM MODAL --}}
<div class="modal-backdrop" id="delete-modal" role="dialog" aria-modal="true" aria-labelledby="modal-title">
    <div class="modal">
        <div class="modal-icon">
            <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
        </div>
        <div class="modal-title" id="modal-title">Hapus Data</div>
        <p class="modal-desc" id="modal-desc">Apakah Anda yakin ingin menghapus data ini? Tindakan ini tidak dapat dibatalkan.</p>
        <div class="modal-actions">
            <button class="btn btn-secondary" onclick="closeDeleteModal()">Batal</button>
            <button class="btn btn-danger" id="modal-confirm-btn">Hapus</button>
        </div>
    </div>
</div>

<div class="app-layout">
    {{-- SIDEBAR --}}
    <aside class="sidebar" id="sidebar" aria-label="Navigasi utama">
        {{-- Logo --}}
        <a href="{{ route('dashboard') }}" class="sidebar-logo" style="text-decoration:none;">
            <div class="sidebar-logo-icon" aria-hidden="true">
                <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="white">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <div class="sidebar-logo-text">
                <strong>Logbook Magang</strong>
                <span>Sistem Informasi</span>
            </div>
        </a>

        {{-- Navigation --}}
        <nav class="sidebar-nav" aria-label="Menu navigasi">
            <a href="{{ route('dashboard') }}"
               class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
               aria-current="{{ request()->routeIs('dashboard') ? 'page' : 'false' }}">
                <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-4 0a1 1 0 01-1-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 01-1 1h-2z"/>
                </svg>
                Dashboard
            </a>

            <a href="{{ route('progress') }}"
               class="sidebar-link {{ request()->routeIs('progress*') ? 'active' : '' }}"
               aria-current="{{ request()->routeIs('progress*') ? 'page' : 'false' }}">
                <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                Progress Magang
            </a>

            <div class="sidebar-section">Data Master</div>

            <a href="{{ route('mahasiswa.index') }}"
               class="sidebar-link {{ request()->routeIs('mahasiswa.*') ? 'active' : '' }}"
               aria-current="{{ request()->routeIs('mahasiswa.*') ? 'page' : 'false' }}">
                <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
                Data Mahasiswa
            </a>

            <a href="{{ route('magang.index') }}"
               class="sidebar-link {{ request()->routeIs('magang.*') ? 'active' : '' }}"
               aria-current="{{ request()->routeIs('magang.*') ? 'page' : 'false' }}">
                <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                Data Magang
            </a>

            <div class="sidebar-section">Pengaturan</div>

            <a href="{{ route('kop-surat.index') }}"
               class="sidebar-link {{ request()->routeIs('kop-surat.*') ? 'active' : '' }}"
               aria-current="{{ request()->routeIs('kop-surat.*') ? 'page' : 'false' }}">
                <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Kop Surat
            </a>
        </nav>

        <div class="sidebar-footer">
            Sistem Logbook Magang v1.0
        </div>
    </aside>

    {{-- MAIN AREA --}}
    <div class="main-area">
        {{-- TOPBAR --}}
        <header class="topbar">
            <button class="hamburger-btn" id="hamburger-btn" onclick="toggleSidebar()" aria-label="Buka menu navigasi" aria-expanded="false" aria-controls="sidebar">
                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>

            <div class="topbar-title">
                @yield('page-title', 'Logbook Magang')
            </div>

            <div class="topbar-actions">
                <div class="topbar-user" title="Administrator">
                    <div class="topbar-avatar" aria-hidden="true">A</div>
                    <span style="font-size:13px; font-weight:500; color:#374151; display:none;" class="hidden sm:block">Admin</span>
                </div>
            </div>
        </header>

        {{-- PAGE CONTENT --}}
        <main class="page-content animate-page" id="main-content">
            @yield('content')
        </main>
    </div>
</div>

@stack('scripts')

<script>
    // ---- SIDEBAR DRAWER ----
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebar-overlay');
        const btn = document.getElementById('hamburger-btn');
        const isOpen = sidebar.classList.contains('open');
        if (isOpen) { closeSidebar(); } else { openSidebar(); }
    }

    function openSidebar() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebar-overlay');
        const btn = document.getElementById('hamburger-btn');
        sidebar.classList.add('open');
        overlay.classList.add('open');
        btn.setAttribute('aria-expanded', 'true');
        document.body.style.overflow = 'hidden';
    }

    function closeSidebar() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebar-overlay');
        const btn = document.getElementById('hamburger-btn');
        sidebar.classList.remove('open');
        overlay.classList.remove('open');
        btn.setAttribute('aria-expanded', 'false');
        document.body.style.overflow = '';
    }

    // Close sidebar on resize to desktop
    window.addEventListener('resize', () => {
        if (window.innerWidth >= 1024) { closeSidebar(); }
    });

    // ---- TOAST AUTO DISMISS ----
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.toast').forEach(toast => {
            setTimeout(() => {
                if (toast.isConnected) {
                    toast.classList.add('dismissing');
                    setTimeout(() => toast.remove(), 220);
                }
            }, 4500);
        });
    });

    // ---- DELETE CONFIRM MODAL ----
    let _pendingDeleteForm = null;

    function confirmDelete(formEl, message) {
        _pendingDeleteForm = formEl;
        const desc = document.getElementById('modal-desc');
        if (message) desc.textContent = message;
        document.getElementById('delete-modal').classList.add('open');
        document.getElementById('modal-confirm-btn').focus();
    }

    function closeDeleteModal() {
        document.getElementById('delete-modal').classList.remove('open');
        _pendingDeleteForm = null;
    }

    document.getElementById('modal-confirm-btn').addEventListener('click', () => {
        if (_pendingDeleteForm) { _pendingDeleteForm.submit(); }
    });

    // Close modal on backdrop click
    document.getElementById('delete-modal').addEventListener('click', function(e) {
        if (e.target === this) { closeDeleteModal(); }
    });

    // Close on Escape
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') { closeDeleteModal(); closeSidebar(); }
    });
</script>
</body>
</html>