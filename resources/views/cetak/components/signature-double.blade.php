{{-- 
    Komponen Tanda Tangan Double (2 kolom)
    Usage: 
    @include('cetak.components.signature-double', [
        'left' => [
            'title' => 'Mengetahui,',
            'jabatan' => 'Ketua Program Studi',
            'nama' => 'Dr. Nama Lengkap',
            'nip' => '123456789'
        ],
        'right' => [
            'title' => null,
            'jabatan' => 'Dosen Pembimbing Akademik',
            'nama' => 'Nama Dosen',
            'nip' => '987654321'
        ],
        'tanggal' => now()->format('d F Y'),
        'kota' => 'Jakarta'
    ])
--}}
<div class="signature-section">
    <table class="no-border" style="width: 100%;">
        @if(isset($kota) || isset($tanggal))
        <tr>
            <td colspan="2" style="text-align: right; padding-bottom: 10px;">
                {{ $kota ?? setting('kota_institusi', '') }}, {{ $tanggal ?? now()->format('d F Y') }}
            </td>
        </tr>
        @endif
        <tr>
            <td style="width: 50%; text-align: center; vertical-align: top;">
                @if(isset($left['title']))
                    <p>{{ $left['title'] }}</p>
                @endif
                <p>{{ $left['jabatan'] ?? '' }}</p>
                <div class="signature-space"></div>
                <p style="text-decoration: underline; font-weight: bold;">{{ $left['nama'] ?? '.............................' }}</p>
                @if(isset($left['nip']))
                    <p>NIP. {{ $left['nip'] }}</p>
                @elseif(isset($left['nidn']))
                    <p>NIDN. {{ $left['nidn'] }}</p>
                @endif
            </td>
            <td style="width: 50%; text-align: center; vertical-align: top;">
                @if(isset($right['title']))
                    <p>{{ $right['title'] }}</p>
                @endif
                <p>{{ $right['jabatan'] ?? '' }}</p>
                <div class="signature-space"></div>
                <p style="text-decoration: underline; font-weight: bold;">{{ $right['nama'] ?? '.............................' }}</p>
                @if(isset($right['nip']))
                    <p>NIP. {{ $right['nip'] }}</p>
                @elseif(isset($right['nidn']))
                    <p>NIDN. {{ $right['nidn'] }}</p>
                @endif
            </td>
        </tr>
    </table>
</div>
