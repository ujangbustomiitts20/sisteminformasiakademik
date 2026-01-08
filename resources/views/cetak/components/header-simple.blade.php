{{-- 
    Komponen Header Simple (untuk laporan)
    Usage: @include('cetak.components.header-simple', ['title' => 'Judul', 'subtitle' => 'Sub Judul'])
--}}
<div class="header-simple">
    <h2>{{ setting('institution_name', setting('nama_institusi', 'Institut Teknologi Tangerang Selatan')) }}</h2>
    <h3>{{ $title ?? 'Dokumen' }}</h3>
    @if(isset($subtitle))
        <p>{{ $subtitle }}</p>
    @endif
    <p>Dicetak pada: {{ now()->format('d/m/Y H:i') }}</p>
</div>
