<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Cuti - POLDA JATENG</title>
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
        <div class="logo-text">
            <h1>KEPOLISIAN NEGARA REPUBLIK INDONESIA</h1>
            <h2>DAERAH JAWA TENGAH</h2>
            <h2>BIRO SUMBER DAYA MANUSIA</h2>
            <p class="tribrata">TRIBRATA</p>
        </div>
        
        <h3>SURAT CUTI</h3>
    </div>
    
    <table class="content-table">
        <tr>
            <td class="field-label">Nama</td>
            <td class="field-value">: <span class="data-line">{{ $pengajuan->name ?? '-' }}</span></td>
        </tr>
        <tr>
            <td class="field-label">Pangkat/Gol/NRP/NIP</td>
            <td class="field-value">: <span class="data-line">
                {{ $pengajuan->pangkat ?? '' }}
                @if($pengajuan->golongan ?? false) / {{ $pengajuan->golongan }} @endif
                @if($pengajuan->nrp ?? false) / {{ $pengajuan->nrp }} @endif
                @if($pengajuan->nip ?? false) / {{ $pengajuan->nip }} @endif
            </span></td>
        </tr>
        <tr>
            <td class="field-label">Jabatan</td>
            <td class="field-value">: <span class="data-line">{{ $pengajuan->jabatan ?? '-' }}</span></td>
        </tr>
        <tr>
            <td class="field-label">Jenis cuti</td>
            <td class="field-value">: <span class="data-line">{{ $pengajuan->nama_jenis ?? '-' }}</span></td>
        </tr>
        <tr>
            <td class="field-label">Lama cuti</td>
            <td class="field-value">: <span class="data-line">
                @if(isset($pengajuan->mulai_tgl) && isset($pengajuan->sampai_tgl))
                    @php
                        try {
                            $start = \Carbon\Carbon::parse($pengajuan->mulai_tgl);
                            $end = \Carbon\Carbon::parse($pengajuan->sampai_tgl);
                            $days = $start->diffInDays($end) + 1;
                            echo $days . ' hari';
                        } catch (\Exception $e) {
                            echo '-';
                        }
                    @endphp
                @else
                    -
                @endif
            </span></td>
        </tr>
        <tr>
            <td class="field-label">Mulai tanggal</td>
            <td class="field-value">: <span class="data-line">
                @if(isset($pengajuan->mulai_tgl))
                    @php
                        try {
                            echo \Carbon\Carbon::parse($pengajuan->mulai_tgl)->translatedFormat('d F Y');
                        } catch (\Exception $e) {
                            echo $pengajuan->mulai_tgl;
                        }
                    @endphp
                @else
                    -
                @endif
            </span></td>
        </tr>
        <tr>
            <td class="field-label">Sampai dengan tanggal</td>
            <td class="field-value">: <span class="data-line">
                @if(isset($pengajuan->sampai_tgl))
                    @php
                        try {
                            echo \Carbon\Carbon::parse($pengajuan->sampai_tgl)->translatedFormat('d F Y');
                        } catch (\Exception $e) {
                            echo $pengajuan->sampai_tgl;
                        }
                    @endphp
                @else
                    -
                @endif
            </span></td>
        </tr>
        <tr>
            <td class="field-label">Pergi dari</td>
            <td class="field-value">: <span class="data-line">{{ $pengajuan->pergi_dari ?? '-' }}</span></td>
        </tr>
        <tr>
            <td class="field-label">Tujuan ke</td>
            <td class="field-value">: <span class="data-line">{{ $pengajuan->tujuan ?? '-' }}</span></td>
        </tr>
        <tr>
            <td class="field-label">Transportasi</td>
            <td class="field-value">: <span class="data-line">{{ $pengajuan->transportasi ?? '-' }}</span></td>
        </tr>
        <tr>
            <td class="field-label">Pengikut</td>
            <td class="field-value">: <span class="data-line">{{ $pengajuan->pengikut ?? 'Tidak ada' }}</span></td>
        </tr>
        <tr>
            <td class="field-label">Catatan</td>
            <td class="field-value">: <span class="data-line">{{ $pengajuan->catatan ?? 'Tidak ada' }}</span></td>
        </tr>
    </table>
    
    <div class="footer-space"></div>
    
    <div class="signature-section avoid-break">
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
                    <p>KEPALA BIRO SUMBER DAYA MANUSIA<br>POLDA JATENG</p>
                    <div class="signature-space"></div>
                    <p><strong><u>Dr. NOVIANA TURSANUROHMAD, S.I.K., M.Si.</u></strong></p>
                    <p>KOMISARIS BESAR POLISI</p>
                    <p>NRP 75110779</p>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>