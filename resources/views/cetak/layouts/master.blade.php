<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>@yield('title', 'Dokumen') - {{ setting('institution_name', setting('nama_institusi', 'Institut Teknologi Tangerang Selatan')) }}</title>
    <style>
        /* ===== BASE STYLES ===== */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #333;
        }

        /* ===== PAGE SETTINGS ===== */
        @page {
            margin: @yield('page-margin', '15mm 15mm 20mm 15mm');
        }

        /* ===== HEADER STYLES ===== */
        .kop-surat {
            border-bottom: 3px double #333;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .kop-surat table {
            width: 100%;
            border: none;
        }

        .kop-surat td {
            border: none;
            vertical-align: middle;
        }

        .kop-surat .logo {
            width: 80px;
            text-align: center;
        }

        .kop-surat .logo img {
            max-width: 70px;
            max-height: 70px;
        }

        .kop-surat .institusi {
            text-align: center;
            padding: 0 10px;
        }

        .kop-surat .institusi h1 {
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 2px;
            color: #000;
        }

        .kop-surat .institusi h2 {
            font-size: 18px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 3px;
            color: #000;
        }

        .kop-surat .institusi p {
            font-size: 10px;
            margin: 1px 0;
            color: #333;
        }

        /* ===== SIMPLE HEADER (untuk laporan) ===== */
        .header-simple {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #333;
        }

        .header-simple h2 {
            font-size: 14px;
            margin: 0;
            text-transform: uppercase;
        }

        .header-simple h3 {
            font-size: 16px;
            margin: 5px 0;
        }

        .header-simple p {
            font-size: 10px;
            color: #666;
            margin: 2px 0;
        }

        /* ===== DOCUMENT TITLE ===== */
        .doc-title {
            text-align: center;
            margin: 20px 0;
        }

        .doc-title h3 {
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
            text-decoration: underline;
            margin-bottom: 5px;
        }

        .doc-title p {
            font-size: 11px;
        }

        /* ===== TABLE STYLES ===== */
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
        }

        table.bordered th,
        table.bordered td {
            border: 1px solid #333;
            padding: 6px 8px;
        }

        table.bordered th {
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: center;
        }

        table.striped tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        table.no-border,
        table.no-border th,
        table.no-border td {
            border: none;
        }

        /* ===== INFO TABLE (untuk data mahasiswa/dosen) ===== */
        table.info-table {
            width: auto;
            margin: 15px 0;
        }

        table.info-table td {
            border: none;
            padding: 3px 10px 3px 0;
            vertical-align: top;
        }

        table.info-table td:first-child {
            width: 150px;
            font-weight: normal;
        }

        table.info-table td:nth-child(2) {
            width: 10px;
            text-align: center;
        }

        /* ===== STATS BOX ===== */
        .stats-container {
            text-align: center;
            margin: 15px 0;
        }

        .stats-box {
            display: inline-block;
            margin: 5px 10px;
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            text-align: center;
            min-width: 80px;
        }

        .stats-box h3 {
            font-size: 18px;
            margin: 0;
            color: #333;
        }

        .stats-box small {
            font-size: 9px;
            color: #666;
        }

        /* ===== BADGE STYLES ===== */
        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
        }

        .badge-success { background-color: #28a745; color: white; }
        .badge-primary { background-color: #007bff; color: white; }
        .badge-info { background-color: #17a2b8; color: white; }
        .badge-warning { background-color: #ffc107; color: #333; }
        .badge-danger { background-color: #dc3545; color: white; }
        .badge-secondary { background-color: #6c757d; color: white; }

        /* ===== TEXT UTILITIES ===== */
        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .text-right { text-align: right; }
        .text-bold { font-weight: bold; }
        .text-italic { font-style: italic; }
        .text-small { font-size: 9px; }
        .text-muted { color: #666; }

        /* ===== SPACING UTILITIES ===== */
        .mt-1 { margin-top: 5px; }
        .mt-2 { margin-top: 10px; }
        .mt-3 { margin-top: 15px; }
        .mt-4 { margin-top: 20px; }
        .mb-1 { margin-bottom: 5px; }
        .mb-2 { margin-bottom: 10px; }
        .mb-3 { margin-bottom: 15px; }
        .mb-4 { margin-bottom: 20px; }

        /* ===== SECTION TITLE ===== */
        .section-title {
            background: #f5f5f5;
            padding: 8px 10px;
            margin: 15px 0 10px;
            font-weight: bold;
            border-left: 3px solid #333;
        }

        /* ===== SIGNATURE SECTION ===== */
        .signature-section {
            margin-top: 40px;
            page-break-inside: avoid;
        }

        .signature-box {
            display: inline-block;
            width: 45%;
            text-align: center;
            vertical-align: top;
        }

        .signature-box.left {
            float: left;
        }

        .signature-box.right {
            float: right;
        }

        .signature-box p {
            margin: 3px 0;
        }

        .signature-space {
            height: 60px;
        }

        .signature-line {
            border-bottom: 1px solid #333;
            width: 200px;
            margin: 0 auto;
        }

        /* ===== FOOTER ===== */
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 9px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 5px;
        }

        .footer-simple {
            margin-top: 30px;
            text-align: right;
            font-size: 9px;
            color: #666;
        }

        /* ===== PAGE BREAK ===== */
        .page-break {
            page-break-after: always;
        }

        .no-break {
            page-break-inside: avoid;
        }

        /* ===== WATERMARK ===== */
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 80px;
            color: rgba(0, 0, 0, 0.05);
            z-index: -1;
        }

        /* ===== CUSTOM STYLES ===== */
        @yield('styles')
    </style>
</head>
<body>
    @hasSection('watermark')
        <div class="watermark">@yield('watermark')</div>
    @endif

    @yield('content')

    @hasSection('footer')
        @yield('footer')
    @endif
</body>
</html>
