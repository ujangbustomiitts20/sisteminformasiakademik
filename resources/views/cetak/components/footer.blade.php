{{-- 
    Komponen Footer
    Usage: @include('cetak.components.footer')
--}}
<div class="footer-simple">
    <p>{{ setting('nama_institusi', 'SIAKAD') }} - Sistem Informasi Akademik</p>
    <p>Dicetak pada: {{ now()->format('d/m/Y H:i:s') }}</p>
</div>
