<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $pengajuan->jenis_surat }}</title>
    <style>
        @page {
            margin: 2cm 2cm;
        }
        
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            line-height: 1.6;
            color: #000;
        }
        
        .kop-surat {
            text-align: center;
            border-bottom: 3px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        
        .kop-surat h2 {
            margin: 0;
            font-size: 18pt;
            font-weight: bold;
        }
        
        .kop-surat h3 {
            margin: 5px 0;
            font-size: 14pt;
            font-weight: normal;
        }
        
        .kop-surat p {
            margin: 2px 0;
            font-size: 10pt;
        }
        
        .nomor-surat {
            text-align: center;
            margin: 30px 0 20px 0;
        }
        
        .nomor-surat h4 {
            margin: 5px 0;
            font-size: 14pt;
            text-decoration: underline;
        }
        
        .nomor-surat p {
            margin: 0;
            font-size: 11pt;
        }
        
        .content {
            text-align: justify;
            margin: 30px 0;
        }
        
        .content p {
            margin: 10px 0;
        }
        
        .data-mahasiswa {
            margin: 20px 0 20px 50px;
        }
        
        .data-mahasiswa table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .data-mahasiswa td {
            padding: 3px 0;
            vertical-align: top;
        }
        
        .data-mahasiswa td:first-child {
            width: 150px;
        }
        
        .data-mahasiswa td:nth-child(2) {
            width: 20px;
            text-align: center;
        }
        
        .ttd {
            margin-top: 50px;
            text-align: right;
            padding-right: 50px;
        }
        
        .ttd p {
            margin: 5px 0;
        }
        
        .ttd .nama {
            margin-top: 80px;
            font-weight: bold;
            text-decoration: underline;
        }
        
        .footer {
            margin-top: 30px;
            font-size: 10pt;
            font-style: italic;
        }
    </style>
</head>
<body>
    <!-- Kop Surat -->
    <div class="kop-surat">
        <h2>UNIVERSITAS CONTOH</h2>
        <h3>FAKULTAS TEKNOLOGI INFORMASI</h3>
        <p>Jalan Raya Kampus No. 123, Telp. (021) 12345678</p>
        <p>Email: info@universitascontoh.ac.id | Website: www.universitascontoh.ac.id</p>
    </div>
    
    <!-- Nomor Surat -->
    <div class="nomor-surat">
        <h4>{{ strtoupper($pengajuan->jenis_surat) }}</h4>
        <p>Nomor: {{ $pengajuan->nomor_surat }}</p>
    </div>
    
    <!-- Content -->
    <div class="content">
        <p>Yang bertanda tangan di bawah ini, Dekan Fakultas Teknologi Informasi Universitas Contoh, menerangkan bahwa:</p>
        
        <div class="data-mahasiswa">
            <table>
                <tr>
                    <td>Nama</td>
                    <td>:</td>
                    <td><strong>{{ $mahasiswa->nama }}</strong></td>
                </tr>
                <tr>
                    <td>NIM</td>
                    <td>:</td>
                    <td><strong>{{ $mahasiswa->nim }}</strong></td>
                </tr>
                <tr>
                    <td>Program Studi</td>
                    <td>:</td>
                    <td>{{ $mahasiswa->programStudi->nama ?? '-' }}</td>
                </tr>
                <tr>
                    <td>Angkatan</td>
                    <td>:</td>
                    <td>{{ $mahasiswa->angkatan }}</td>
                </tr>
                <tr>
                    <td>Semester</td>
                    <td>:</td>
                    <td>{{ $mahasiswa->semester_aktif }}</td>
                </tr>
            </table>
        </div>
        
        @if($pengajuan->jenis_surat == 'Surat Keterangan Aktif Kuliah')
        <p>
            Adalah benar mahasiswa yang terdaftar dan <strong>aktif kuliah</strong> pada Semester {{ $mahasiswa->semester_aktif }} 
            Tahun Akademik {{ date('Y') }}/{{ date('Y') + 1 }} di Universitas Contoh.
        </p>
        @elseif($pengajuan->jenis_surat == 'Surat Pengantar Penelitian')
        <p>
            Mahasiswa tersebut di atas akan melaksanakan kegiatan <strong>penelitian</strong> dalam rangka penyusunan 
            skripsi/tugas akhir sebagai salah satu syarat untuk menyelesaikan studi pada program Sarjana (S1) 
            di Universitas Contoh.
        </p>
        @elseif($pengajuan->jenis_surat == 'Surat Pengantar Magang/PKL')
        <p>
            Mahasiswa tersebut di atas akan melaksanakan kegiatan <strong>magang/praktik kerja lapangan (PKL)</strong> 
            sebagai salah satu persyaratan akademik untuk menyelesaikan studi pada program Sarjana (S1) 
            di Universitas Contoh.
        </p>
        @elseif($pengajuan->jenis_surat == 'Surat Keterangan Berkelakuan Baik')
        <p>
            Mahasiswa tersebut di atas selama mengikuti perkuliahan di Universitas Contoh 
            <strong>berkelakuan baik</strong> dan tidak pernah terlibat dalam tindakan yang melanggar 
            peraturan akademik maupun tata tertib kampus.
        </p>
        @elseif($pengajuan->jenis_surat == 'Surat Keterangan Lulus')
        <p>
            Mahasiswa tersebut di atas telah dinyatakan <strong>lulus</strong> dan berhak menyandang gelar 
            Sarjana Komputer (S.Kom) setelah menyelesaikan seluruh persyaratan akademik 
            di Universitas Contoh.
        </p>
        @else
        <p>
            Mahasiswa tersebut di atas adalah mahasiswa aktif yang terdaftar di Universitas Contoh 
            dan memerlukan surat keterangan ini untuk keperluan <strong>{{ $pengajuan->keperluan }}</strong>.
        </p>
        @endif
        
        @if($pengajuan->ditujukan_kepada)
        <p>
            Surat keterangan ini dibuat untuk dipergunakan sebagai 
            @if(Str::contains(strtolower($pengajuan->jenis_surat), 'pengantar'))
                permohonan izin kepada <strong>{{ $pengajuan->ditujukan_kepada }}</strong>.
            @else
                persyaratan kepada <strong>{{ $pengajuan->ditujukan_kepada }}</strong>.
            @endif
        </p>
        @endif
        
        <p>
            Demikian surat keterangan ini dibuat dengan sebenarnya untuk dapat dipergunakan sebagaimana mestinya.
        </p>
    </div>
    
    <!-- TTD -->
    <div class="ttd">
        <p>{{ \Carbon\Carbon::parse($pengajuan->tanggal_diproses)->translatedFormat('d F Y') }}</p>
        <p>Dekan Fakultas Teknologi Informasi,</p>
        <p class="nama">Dr. Ahmad Hidayat, M.Kom</p>
        <p>NIP. 19750815 200312 1 001</p>
    </div>
    
    <!-- Footer -->
    <div class="footer">
        <p>Dokumen ini digenerate otomatis oleh sistem pada {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>
</body>
</html>
