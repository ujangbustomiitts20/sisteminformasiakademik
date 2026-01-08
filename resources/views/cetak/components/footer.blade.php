{{-- 
    Komponen Footer
    Usage: @include('cetak.components.footer')
--}}
<div class="footer-simple">
    <p>{{ setting('institution_name', setting('nama_institusi', 'Institut Teknologi Tangerang Selatan')) }} - {{ setting('app_name', 'NADI ITTS') }}</p>
    <p>Dicetak pada: {{ now()->format('d/m/Y H:i:s') }}</p>
</div>
