{{-- 
    Komponen Kop Surat Resmi
    Usage: @include('cetak.components.kop-surat')
--}}
<div class="kop-surat">
    <table>
        <tr>
            <td class="logo">
                @if(setting('logo_institusi'))
                    <img src="{{ public_path('storage/' . setting('logo_institusi')) }}" alt="Logo">
                @else
                    <img src="{{ public_path('images/logo.png') }}" alt="Logo" onerror="this.style.display='none'">
                @endif
            </td>
            <td class="institusi">
                <h1>{{ setting('nama_yayasan', 'YAYASAN PENDIDIKAN') }}</h1>
                <h2>{{ setting('nama_institusi', 'UNIVERSITAS') }}</h2>
                <p>{{ setting('alamat_institusi', 'Alamat Institusi') }}</p>
                <p>
                    @if(setting('telepon_institusi'))
                        Telp: {{ setting('telepon_institusi') }}
                    @endif
                    @if(setting('email_institusi'))
                        | Email: {{ setting('email_institusi') }}
                    @endif
                    @if(setting('website_institusi'))
                        | {{ setting('website_institusi') }}
                    @endif
                </p>
            </td>
            <td class="logo">
                {{-- Space for second logo if needed --}}
            </td>
        </tr>
    </table>
</div>
