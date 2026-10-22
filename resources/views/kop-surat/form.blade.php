@extends('layouts.app')

@section('page-title', $kopSurat ? 'Edit Kop Surat' : 'Tambah Kop Surat')

@section('content')
<div class="page-header">
    <div class="page-header-inner">
        <div>
            <div style="margin-bottom:4px;">
                <a href="{{ route('kop-surat.index') }}" style="font-size:13px; color:#64748B; text-decoration:none; display:inline-flex; align-items:center; gap:4px;">
                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    Pengaturan Kop Surat
                </a>
            </div>
            <h1 class="page-title">{{ $kopSurat ? 'Edit Kop Surat' : 'Tambah Kop Surat' }}</h1>
            <p class="page-subtitle">Konfigurasi kop kampus atau instansi mitra yang digunakan pada cetak dokumen</p>
        </div>
    </div>
</div>

<div style="display:grid; grid-template-columns:minmax(0, 1.4fr) minmax(320px, 1fr); gap:28px; align-items:start;">
    {{-- FORM / SETTINGS --}}
    <div class="card" style="padding:28px;">
        <div class="card-title" style="font-size:16px; margin-bottom:20px; padding-bottom:12px; border-bottom:1px solid #F1F5F9;">
            Pengaturan Parameter Kop
        </div>
        <form action="{{ $kopSurat ? route('kop-surat.update', $kopSurat) : route('kop-surat.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @if ($kopSurat) @method('PUT') @endif

            <div style="display:flex; flex-direction:column; gap:18px;">
                <div>
                    <label class="form-label">Jenis Kop <span class="required">*</span></label>
                    <select name="jenis" id="kop-jenis" required class="form-select {{ $errors->has('jenis') ? 'error' : '' }}">
                        <option value="kampus" {{ old('jenis', $kopSurat?->jenis) === 'kampus' ? 'selected' : '' }}>Kampus / Program Studi</option>
                        <option value="instansi" {{ old('jenis', $kopSurat?->jenis) === 'instansi' ? 'selected' : '' }}>Instansi / Lokasi Magang</option>
                    </select>
                    @error('jenis') <div class="form-error">{{ $message }}</div> @enderror
                </div>

                <div>
                    <label class="form-label">Nama Instansi / Perguruan Tinggi <span class="required">*</span></label>
                    <input type="text" name="nama_instansi" id="kop-instansi" value="{{ old('nama_instansi', $kopSurat?->nama_instansi) }}" required
                        class="form-input {{ $errors->has('nama_instansi') ? 'error' : '' }}"
                        placeholder="Contoh: Politeknik Negeri Nusa Utara">
                    @error('nama_instansi') <div class="form-error">{{ $message }}</div> @enderror
                </div>

                <div>
                    <label class="form-label">Alamat Lengkap</label>
                    <textarea name="alamat" id="kop-alamat" rows="2"
                        class="form-textarea {{ $errors->has('alamat') ? 'error' : '' }}"
                        placeholder="Alamat lengkap instansi/kampus">{{ old('alamat', $kopSurat?->alamat) }}</textarea>
                    @error('alamat') <div class="form-error">{{ $message }}</div> @enderror
                </div>

                <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                    <div>
                        <label class="form-label">Telepon / Fax</label>
                        <input type="text" name="telepon" id="kop-telepon" value="{{ old('telepon', $kopSurat?->telepon) }}"
                            class="form-input {{ $errors->has('telepon') ? 'error' : '' }}"
                            placeholder="(0432) 24745">
                        @error('telepon') <div class="form-error">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label class="form-label">Website</label>
                        <input type="text" name="website" id="kop-website" value="{{ old('website', $kopSurat?->website) }}"
                            class="form-input {{ $errors->has('website') ? 'error' : '' }}"
                            placeholder="polnustar.ac.id">
                        @error('website') <div class="form-error">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div>
                    <label class="form-label">Logo Instansi / Kampus</label>
                    @if ($kopSurat?->logo_path)
                        <div style="margin-bottom:12px; padding:12px; background:#F8FAFC; border:1px solid #E2E8F0; border-radius:8px; display:inline-flex; align-items:center; gap:12px;">
                            <img src="{{ asset('storage/'.$kopSurat->logo_path) }}" alt="Logo {{ $kopSurat->nama_instansi }}"
                                 style="height:44px; object-fit:contain;"
                                 onerror="this.onerror=null; this.parentElement.innerHTML='<span style=\'font-size:12px; color:#94A3B8;\'>[Logo tidak ditemukan]</span>';">
                            <span style="font-size:12px; color:#64748B;">Logo tersimpan. Unggah baru untuk mengganti.</span>
                        </div>
                    @endif
                    <input type="file" name="logo" accept="image/png,image/jpg,image/jpeg,image/svg+xml" class="form-file">
                    <div class="form-hint">Maks 2MB. Format: PNG, JPG, SVG. Diutamakan transparan.</div>
                    @error('logo') <div class="form-error">{{ $message }}</div> @enderror
                </div>

                <div style="display:flex; align-items:center; gap:10px; padding-top:12px; border-top:1px solid #F1F5F9; margin-top:8px;">
                    <button type="submit" class="btn btn-primary">
                        {{ $kopSurat ? 'Simpan Perubahan' : 'Simpan Kop Surat' }}
                    </button>
                    <a href="{{ route('kop-surat.index') }}" class="btn btn-secondary">Batal</a>
                </div>
            </div>
        </form>
    </div>

    {{-- LIVE DOCUMENT PREVIEW --}}
    <div class="card" style="padding:24px; background:#F8FAFC; border:1px solid #E2E8F0;">
        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:16px;">
            <div style="font-size:12px; font-weight:800; color:#2563EB; text-transform:uppercase; letter-spacing:0.08em;">
                DOCUMENT PREVIEW
            </div>
            <span class="badge badge-info" id="preview-badge">
                {{ old('jenis', $kopSurat?->jenis) === 'instansi' ? 'Instansi Mitra' : 'Kampus / Prodi' }}
            </span>
        </div>

        <div style="background:#FFFFFF; border:1px solid #E2E8F0; border-radius:12px; padding:24px 20px; box-shadow:0 4px 12px rgba(0,0,0,0.03);">
            <div style="display:flex; align-items:center; gap:16px; border-bottom:3px double #0F172A; padding-bottom:14px; margin-bottom:20px;">
                @if ($kopSurat?->logo_path)
                    <img src="{{ asset('storage/'.$kopSurat->logo_path) }}" alt="Logo"
                         style="height:54px; max-width:90px; object-fit:contain; flex-shrink:0;"
                         onerror="this.style.display='none';">
                @else
                    <div style="width:48px; height:48px; border-radius:8px; background:#EFF6FF; border:1px dashed #BFDBFE; display:flex; align-items:center; justify-content:center; color:#2563EB; font-size:11px; font-weight:800; flex-shrink:0;">
                        LOGO
                    </div>
                @endif
                <div style="min-width:0; flex:1; text-align:center;">
                    <div id="preview-instansi" style="font-size:14px; font-weight:800; color:#0F172A; text-transform:uppercase; line-height:1.25;">
                        {{ old('nama_instansi', $kopSurat?->nama_instansi) ?: 'NAMA INSTANSI / KAMPUS' }}
                    </div>
                    <div id="preview-alamat" style="font-size:11px; color:#475569; margin-top:4px; line-height:1.35;">
                        {{ old('alamat', $kopSurat?->alamat) ?: 'Alamat instansi atau kampus akan tampil di sini...' }}
                    </div>
                    <div id="preview-kontak" style="font-size:10.5px; color:#64748B; margin-top:3px;">
                        {{ implode(' · ', array_filter([old('telepon', $kopSurat?->telepon) ? 'Telp: '.old('telepon', $kopSurat?->telepon) : null, old('website', $kopSurat?->website)])) ?: 'Kontak telepon dan website' }}
                    </div>
                </div>
            </div>

            {{-- DUMMY DOCUMENT BODY PREVIEW --}}
            <div style="opacity:0.45; font-size:11px; color:#64748B; display:flex; flex-direction:column; gap:8px;">
                <div style="height:8px; width:40%; background:#CBD5E1; border-radius:4px; margin-bottom:6px;"></div>
                <div style="height:6px; width:100%; background:#E2E8F0; border-radius:3px;"></div>
                <div style="height:6px; width:92%; background:#E2E8F0; border-radius:3px;"></div>
                <div style="height:6px; width:96%; background:#E2E8F0; border-radius:3px;"></div>
                <div style="height:6px; width:70%; background:#E2E8F0; border-radius:3px;"></div>
            </div>
        </div>
        <p style="font-size:11.5px; color:#94A3B8; margin-top:14px; text-align:center;">
            Tampilan di atas adalah ilustrasi cetak kop surat pada dokumen logbook & presensi.
        </p>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const instansiInput = document.getElementById('kop-instansi');
        const alamatInput = document.getElementById('kop-alamat');
        const teleponInput = document.getElementById('kop-telepon');
        const websiteInput = document.getElementById('kop-website');
        const jenisInput = document.getElementById('kop-jenis');

        const prevInstansi = document.getElementById('preview-instansi');
        const prevAlamat = document.getElementById('preview-alamat');
        const prevKontak = document.getElementById('preview-kontak');
        const prevBadge = document.getElementById('preview-badge');

        function updatePreview() {
            if (instansiInput && prevInstansi) {
                prevInstansi.textContent = instansiInput.value.trim() || 'NAMA INSTANSI / KAMPUS';
            }
            if (alamatInput && prevAlamat) {
                prevAlamat.textContent = alamatInput.value.trim() || 'Alamat instansi atau kampus akan tampil di sini...';
            }
            if (prevKontak) {
                const tel = teleponInput ? teleponInput.value.trim() : '';
                const web = websiteInput ? websiteInput.value.trim() : '';
                const parts = [];
                if (tel) parts.push('Telp: ' + tel);
                if (web) parts.push(web);
                prevKontak.textContent = parts.length > 0 ? parts.join(' · ') : 'Kontak telepon dan website';
            }
            if (jenisInput && prevBadge) {
                prevBadge.textContent = jenisInput.value === 'instansi' ? 'Instansi Mitra' : 'Kampus / Prodi';
            }
        }

        [instansiInput, alamatInput, teleponInput, websiteInput, jenisInput].forEach(el => {
            if (el) el.addEventListener('input', updatePreview);
            if (el) el.addEventListener('change', updatePreview);
        });
    });
</script>
@endpush
@endsection