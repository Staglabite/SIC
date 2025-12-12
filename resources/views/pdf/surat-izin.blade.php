<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Izin Jalan - POLDA JATENG</title>
    <style>
        @page {
            margin: 0.5in;
        }
        
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            line-height: 1.5;
            margin: 0;
            padding: 0;
        }
        
        .header {
            text-align: center;
            margin-bottom: 15px;
            padding-top: 10px;
        }
        
        .header h1 {
            margin: 0;
            font-size: 14pt;
            font-weight: bold;
        }
        
        .header h2 {
            margin: 0;
            font-size: 12pt;
            font-weight: bold;
        }
        
        .header h3 {
            margin: 10px 0;
            font-size: 12pt;
            font-weight: bold;
            text-decoration: underline;
        }
        
        .logo-text {
            text-align: center;
        }
        
        .tribrata {
            font-weight: bold;
            font-size: 10pt;
            margin-top: 5px;
        }
        
        .content-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        
        .content-table tr td:first-child {
            width: 30%;
            vertical-align: top;
            white-space: nowrap;
        }
        
        .content-table tr td {
            padding: 3px 0;
        }
        
        .field-label {
            font-weight: normal;
        }
        
        .field-value {
            font-weight: normal;
            padding-left: 5px;
        }
        
        .signature-section {
            margin-top: 30px;
            page-break-inside: avoid;
        }
        
        .signature-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .signature-table td {
            vertical-align: top;
            padding: 5px;
        }
        
        .tembusan {
            margin-top: 20px;
            page-break-inside: avoid;
        }
        
        .tembusan ol {
            margin: 5px 0;
            padding-left: 20px;
        }
        
        .tembusan li {
            margin-bottom: 3px;
        }
        
        .data-line {
            display: inline-block;
            min-width: 250px;
            border-bottom: 1px dotted #000;
            margin-left: 5px;
        }
        
        .no-data {
            color: #666;
            font-style: italic;
        }
        
        .footer-space {
            height: 40px;
        }
        
        .signature-space {
            height: 80px;
        }
        
        .page-break {
            page-break-after: always;
        }
        
        /* Untuk PDF agar tidak terpotong */
        .avoid-break {
            page-break-inside: avoid;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo-section">
            <!-- Logo akan dimasukkan jika ada -->
            <div class="logo-text">
                <h1>KEPOLISIAN NEGARA REPUBLIK INDONESIA</h1>
                <h2>DAERAH JAWA TENGAH</h2>
                <h2>BIRO SUMBER DAYA MANUSIA</h2>
                <p class="tribrata">TRIBRATA</p>
            </div>
        </div>
        
        <h3>SURAT IZIN JALAN</h3>
    </div>
    
    <table class="content-table">
        <tr>
            <td class="field-label">Nama</td>
            <td class="field-value">: <span class="data-value">{{ $pengajuan->name ?? 'N/A' }}</span></td>
        </tr>
        <tr>
            <td class="field-label">Pangkat/Gol/NRP/NIP</td>
            <td class="field-value">: <span class="data-value">
                {{ $pengajuan->pangkat ?? '' }} / 
                {{ $pengajuan->golongan ?? '' }} / 
                {{ $pengajuan->nrp ?? '' }} / 
                {{ $pengajuan->nip ?? '' }}
            </span></td>
        </tr>
        <tr>
            <td class="field-label">Jabatan</td>
            <td class="field-value">: <span class="data-value">{{ $pengajuan->jabatan ?? 'N/A' }}</span></td>
        </tr>
        <tr>
            <td class="field-label">Pengikut</td>
            <td class="field-value">: <span class="data-value">{{ $pengajuan->pengikut ?? 'Tidak ada' }}</span></td>
        </tr>
        <tr>
            <td class="field-label">Pergi dari</td>
            <td class="field-value">: <span class="data-value">{{ $pengajuan->pergi_dari ?? 'N/A' }}</span></td>
        </tr>
        <tr>
            <td class="field-label">Tujuan ke</td>
            <td class="field-value">: <span class="data-value">{{ $pengajuan->tujuan ?? 'N/A' }}</span></td>
        </tr>
        <tr>
            <td class="field-label">Keperluan</td>
            <td class="field-value">: <span class="data-value">{{ $pengajuan->keperluan ?? 'N/A' }}</span></td>
        </tr>
        <tr>
            <td class="field-label">Transportasi</td>
            <td class="field-value">: <span class="data-value">{{ $pengajuan->transportasi ?? 'N/A' }}</span></td>
        </tr>
        <tr>
            <td class="field-label">Berangkat tanggal</td>
            <td class="field-value">: <span class="data-value">
                @if($pengajuan->tgl_berangkat)
                    {{ \Carbon\Carbon::parse($pengajuan->tgl_berangkat)->translatedFormat('d F Y') }}
                @else
                    N/A
                @endif
            </span></td>
        </tr>
        <tr>
            <td class="field-label">Kembali tanggal</td>
            <td class="field-value">: <span class="data-value">
                @if($pengajuan->tgl_kembali)
                    {{ \Carbon\Carbon::parse($pengajuan->tgl_kembali)->translatedFormat('d F Y') }}
                @else
                    N/A
                @endif
            </span></td>
        </tr>
        <tr>
            <td class="field-label">Catatan</td>
            <td class="field-value">: <span class="data-value">{{ $pengajuan->catatan ?? 'Tidak ada' }}</span></td>
        </tr>
    </table>
    
    <div class="signature-section">
        <table class="signature-table">
            <tr>
                <td width="50%">
                    <table>
                        <tr>
                            <td>Dikeluarkan di</td>
                            <td>: Semarang</td>
                        </tr>
                        <tr>
                            <td>pada tanggal</td>
                            <td>: {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</td>
                        </tr>
                    </table>
                </td>
                <td width="50%" style="text-align: center;">
                    <div class="spacer"></div>
                    <p>KEPALA BIRO SUMBER DAYA MANUSIA<br>POLDA JATENG</p>
                    <br><br><br><br>
                    <p><strong><u>Dr. NOVIANA TURSANUROHMAD, S.I.K., M.Si.</u></strong></p>
                    <p>KOMISARIS BESAR POLISI</p>
                    <p>NRP 75110779</p>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>