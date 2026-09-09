@php
    $documentType = $documentType ?? 'konsultasi';
    $web = $web ?? false;
    $kopLines = [];
    $logoPath = null;
    $logoSrc = null;
    $logoWidth = 2.3;
    $logoHeight = $logoWidth;

    if ($kop) {
        if ($kop->jenis === 'kampus') {
            $kopLines[] = ['text' => 'KEMENTERIAN PENDIDIKAN TINGGI, SAINS,', 'class' => 'kop-ministry'];
            $kopLines[] = ['text' => 'DAN TEKNOLOGI', 'class' => 'kop-ministry'];
        }

        $kopLines[] = ['text' => $kop->nama_instansi, 'class' => 'kop-instansi'];
        $kopLines[] = [
            'text' => ($kop->jenis === 'kampus' ? 'JURUSAN' : 'UNIT').' TEKNOLOGI INFORMATIKA',
            'class' => 'kop-jurusan',
        ];

        if ($kop->alamat) {
            $kopLines[] = ['text' => $kop->alamat, 'class' => 'kop-contact'];
        }

        if ($kop->telepon) {
            $kopLines[] = ['text' => 'Telepon '.$kop->telepon, 'class' => 'kop-contact'];
        }

        if ($kop->website) {
            $website = preg_match('#^https?://#i', $kop->website) ? $kop->website : 'http://'.$kop->website;
            $kopLines[] = ['text' => 'Laman : '.$website, 'class' => 'kop-contact'];
        }

        if ($kop->logo_path && file_exists(storage_path('app/public/'.$kop->logo_path))) {
            $logoPath = storage_path('app/public/'.$kop->logo_path);
            $logoSrc = $web ? asset('storage/'.$kop->logo_path) : $logoPath;
            $dim = @getimagesize($logoPath);

            if ($dim) {
                $logoHeight = round($logoWidth * ($dim[1] / $dim[0]), 2);
            }
        }
    }
@endphp

@if ($kop)
    <table class="kop-table">
        <tr>
            <td class="kop-logo">
                @if ($logoSrc)
                    <img class="kop-img" src="{{ $logoSrc }}" alt=""
                        style="width:{{ $logoWidth }}cm; height:{{ $logoHeight }}cm;">
                @endif
            </td>
            <td class="kop-text">
                @foreach ($kopLines as $line)
                    <div class="{{ $line['class'] }}">{{ $line['text'] }}</div>
                @endforeach
            </td>
            <td class="kop-spacer"></td>
        </tr>
    </table>
    @if ($documentType === 'logbook')
        <div class="kop-gap"></div>
        <div class="kop-title" style="margin-top:4px;">FORM LOGBOOK KEGIATAN MAGANG MAHASISWA</div>
    @else
        @php
            $kopTitles = $documentType === 'presensi'
                ? ['DAFTAR HADIR / PRESENSI MAHASISWA MAGANG', 'MERDEKA BELAJAR KAMPUS MERDEKA']
                : ['LEMBAR KONSULTASI DOSEN PEMBIMBING', 'MAGANG MERDEKA BELAJAR KAMPUS MERDEKA'];
        @endphp
        <div class="kop-gap"></div>
        <div class="kop-title kop-title-first">{{ $kopTitles[0] }}</div>
        <div class="kop-title">{{ $kopTitles[1] }}</div>
    @endif
    <div class="kop-border">&nbsp;</div>
@endif
