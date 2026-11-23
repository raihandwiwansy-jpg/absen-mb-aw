<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Kehadiran - Symphony Marching Band</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 10px; color: #333; margin: 15px; }
        .header-table { width: 100%; border-bottom: 3px double #C6A044; padding-bottom: 10px; margin-bottom: 15px; }
        .org-title { font-size: 16px; font-weight: bold; color: #101D3B; text-transform: uppercase; margin: 0; }
        .school-title { font-size: 12px; font-weight: bold; color: #C6A044; letter-spacing: 1px; margin: 2px 0; }
        .meta-info { font-size: 9px; color: #666; }
        .report-header { text-align: center; margin: 15px 0; }
        .report-title { font-size: 14px; font-weight: bold; color: #101D3B; text-transform: uppercase; letter-spacing: 0.5px; }
        .report-subtitle { font-size: 10px; color: #666; margin-top: 3px; }
        table.data-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table.data-table th { background-color: #101D3B; color: #ffffff; font-weight: bold; padding: 6px 6px; text-align: left; font-size: 9px; border: 1px solid #101D3B; }
        table.data-table td { padding: 5px 6px; border: 1px solid #e5e7eb; font-size: 9px; }
        table.data-table tr:nth-child(even) { background-color: #f9fafb; }
        .badge { display: inline-block; padding: 2px 5px; font-size: 8px; font-weight: bold; border-radius: 3px; text-transform: uppercase; text-align: center; }
        .status-present { background-color: #d1fae5; color: #065f46; }
        .status-late { background-color: #fef3c7; color: #92400e; }
        .status-permission { background-color: #dbeafe; color: #1e40af; }
        .status-sick { background-color: #f3e8ff; color: #6b21a8; }
        .status-absent { background-color: #fee2e2; color: #991b1b; }
        .footer-table { width: 100%; margin-top: 30px; font-size: 10px; }
    </style>
</head>
<body>

    <table class="header-table">
        <tr>
            <td style="width: 80px; text-align: center; vertical-align: middle;">
                <img src="{{ public_path('logo.jpg') }}" style="width: 65px; height: 65px; border-radius: 50%;" alt="Logo">
            </td>
            <td style="vertical-align: middle; padding-left: 10px;">
                <div class="school-title">SMK AL WASLIYAH PERDAGANGAN</div>
                <div class="org-title">SYMPHONY MARCHING BAND</div>
                <div class="meta-info">Sekretariat: Jl. Besar Perdagangan, Kab. Simalungun • Tahun Periode 2026</div>
                <div class="meta-info">Sistem Informasi Manajemen Presensi & Aktivitas Organisasi</div>
            </td>
        </tr>
    </table>

    <div class="report-header">
        <div class="report-title">REKAPITULASI RESMI PRESENSI LATIHAN</div>
        <div class="report-subtitle">Waktu Cetak: {{ $dateGenerated }} WIB</div>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 20px; text-align: center;">No</th>
                <th style="width: 65px;">Tanggal</th>
                <th style="width: 60px;">ID Anggota</th>
                <th>Nama Lengkap</th>
                <th>Divisi</th>
                <th>Instrumen</th>
                <th>Kegiatan Latihan</th>
                <th style="width: 55px; text-align: center;">Status</th>
                <th style="width: 50px; text-align: center;">Jam Masuk</th>
            </tr>
        </thead>
        <tbody>
            @foreach($attendances as $index => $att)
            <tr>
                <td style="text-align: center;">{{ $index + 1 }}</td>
                <td style="font-family: monospace;">{{ $att->attendanceSession->practiceSchedule->date->format('d/m/Y') ?? '-' }}</td>
                <td style="font-family: monospace; font-weight: bold;">{{ $att->member->member_code }}</td>
                <td><strong>{{ $att->member->full_name }}</strong></td>
                <td>{{ $att->member->division->name ?? '-' }}</td>
                <td>{{ $att->member->instrument->name ?? '-' }}</td>
                <td>{{ $att->attendanceSession->practiceSchedule->title ?? '-' }}</td>
                <td style="text-align: center;">
                    <span class="badge status-{{ $att->status }}">{{ $att->status_label }}</span>
                </td>
                <td style="text-align: center; font-family: monospace;">{{ $att->check_in_time ? $att->check_in_time->format('H:i') : '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <table class="footer-table">
        <tr>
            <td style="width: 65%;"></td>
            <td style="width: 35%; text-align: center;">
                <p>Perdagangan, {{ date('d F Y') }}</p>
                <p style="margin-top: 5px; font-weight: bold;">Pembina / Pelatih Marching Band,</p>
                <div style="height: 50px;"></div>
                <p style="font-weight: bold; text-decoration: underline;">Pengurus Symphony Marching Band</p>
                <p style="font-size: 8px; color: #777;">SMK Al Wasliyah Perdagangan</p>
            </td>
        </tr>
    </table>

</body>
</html>

