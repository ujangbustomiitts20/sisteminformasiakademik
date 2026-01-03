{{-- 
    Komponen Tanda Tangan
    Usage: 
    @include('cetak.components.signature', [
        'jabatan' => 'Kepala Bagian Akademik',
        'nama' => 'Dr. Nama Lengkap',
        'nip' => '123456789',
        'tanggal' => now()->format('d F Y'),
        'kota' => 'Jakarta'
    ])
    
    Untuk 2 kolom:
    @include('cetak.components.signature-double', [
        'left' => ['jabatan' => '...', 'nama' => '...'],
        'right' => ['jabatan' => '...', 'nama' => '...'],
    ])
--}}
<div class="signature-section">
    <table class="no-border" style="width: 100%;">
        <tr>
            <td style="width: 50%;"></td>
            <td style="width: 50%; text-align: center;">
                @if(isset($kota))
                    <p>{{ $kota }}, {{ $tanggal ?? now()->format('d F Y') }}</p>
                @else
                    <p>{{ setting('kota_institusi', '') }}, {{ $tanggal ?? now()->format('d F Y') }}</p>
                @endif
                <p>{{ $jabatan ?? 'Pejabat' }}</p>
                <div class="signature-space"></div>
                <p style="text-decoration: underline; font-weight: bold;">{{ $nama ?? '.............................' }}</p>
                @if(isset($nip))
                    <p>NIP. {{ $nip }}</p>
                @endif
            </td>
        </tr>
    </table>
</div>
