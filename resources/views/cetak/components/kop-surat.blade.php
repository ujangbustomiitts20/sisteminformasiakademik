{{-- 
    Komponen Kop Surat Resmi
    Usage: @include('cetak.components.kop-surat')
    
    Data diambil dari Settings > Informasi Institusi
--}}
@php
    $logoPath = setting('institution_logo');
    $logoFullPath = $logoPath ? storage_path('app/public/' . $logoPath) : null;
    $logoExists = $logoFullPath && file_exists($logoFullPath);
    
    // Convert to base64 for PDF compatibility
    $logoBase64 = null;
    if ($logoExists) {
        $logoData = file_get_contents($logoFullPath);
        $logoMime = mime_content_type($logoFullPath);
        $logoBase64 = 'data:' . $logoMime . ';base64,' . base64_encode($logoData);
    }
@endphp
<div class="kop-surat">
    <table>
        <tr>
            <td class="logo">
                @if($logoBase64)
                    <img src="{{ $logoBase64 }}" alt="Logo">
                @elseif(file_exists(public_path('images/logo.png')))
                    @php
                        $defaultLogo = file_get_contents(public_path('images/logo.png'));
                        $defaultLogoBase64 = 'data:image/png;base64,' . base64_encode($defaultLogo);
                    @endphp
                    <img src="{{ $defaultLogoBase64 }}" alt="Logo">
                @endif
            </td>
            <td class="institusi">
                @if(setting('institution_yayasan'))
                <h1>{{ setting('institution_yayasan') }}</h1>
                @endif
                <h2>{{ strtoupper(setting('institution_name', 'UNIVERSITAS')) }}</h2>
                <p>{{ setting('institution_address', 'Alamat Institusi') }}</p>
                <p>
                    @if(setting('contact_phone'))
                        Telp: {{ setting('contact_phone') }}
                    @endif
                    @if(setting('contact_fax'))
                        | Fax: {{ setting('contact_fax') }}
                    @endif
                    @if(setting('contact_email'))
                        | Email: {{ setting('contact_email') }}
                    @endif
                </p>
                @if(setting('contact_website'))
                <p>Website: {{ setting('contact_website') }}</p>
                @endif
            </td>
            <td class="logo">
                {{-- Space for second logo if needed --}}
            </td>
        </tr>
    </table>
</div>
