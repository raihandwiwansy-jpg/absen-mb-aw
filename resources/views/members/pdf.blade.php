<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Daftar Anggota - Symphony Marching Band</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 11px; color: #333; margin: 20px; }
        .header { text-align: center; border-bottom: 2px solid #C6A044; padding-bottom: 12px; margin-bottom: 20px; }
        .logo { width: 70px; height: 70px; border-radius: 50%; }
        .org-title { font-size: 16px; font-weight: bold; color: #101D3B; text-transform: uppercase; margin: 4px 0 2px 0; }
        .school-title { font-size: 12px; font-weight: bold; color: #C6A044; letter-spacing: 1px; }
        .meta-info { font-size: 9px; color: #777; margin-top: 4px; }
        .report-title { font-size: 13px; font-weight: bold; text-align: center; margin: 15px 0; text-transform: uppercase; color: #101D3B; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background-color: #101D3B; color: #ffffff; font-weight: bold; padding: 6px 8px; text-align: left; font-size: 10px; border: 1px solid #101D3B; }
        td { padding: 6px 8px; border: 1px solid #e5e7eb; font-size: 10px; }
        tr:nth-child(even) { background-color: #f9fafb; }
        .badge { display: inline-block; padding: 2px 6px; font-size: 8px; font-weight: bold; border-radius: 4px; text-transform: uppercase; }
        .active { background-color: #d1fae5; color: #065f46; }
        .footer { margin-top: 30px; text-align: right; font-size: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="school-title">SMK AL WASLIYAH PERDAGANGAN</div>
        <div class="org-title">SYMPHONY MARCHING BAND</div>
        <div class="meta-info">Tahun Anggaran / Periode 2026 • Sistem Administrasi Terpadu</div>
    </div>

    <div class="report-title">DAFTAR RESMI ANGGOTA MARCHING BAND</div>

    <table>
        <thead>
            <tr>
                <th style="width: 25px;">No</th>
                <th style="width: 80px;">ID Anggota</th>
                <th>Nama Lengkap</th>
                <th style="width: 50px;">L/P</th>
                <th style="width: 60px;">Kelas</th>
                <th>Divisi</th>
                <th>Instrumen</th>
                <th>Jabatan</th>
                <th style="width: 55px;">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($members as $index => $m)
            <tr>
                <td style="text-align: center;">{{ $index + 1 }}</td>
                <td style="font-family: monospace; font-weight: bold;">{{ $m->member_code }}</td>
                <td><strong>{{ $m->full_name }}</strong></td>
                <td style="text-align: center;">{{ $m->gender === 'male' ? 'L' : 'P' }}</td>
                <td>{{ $m->class ?? '-' }}</td>
                <td>{{ $m->division->name ?? '-' }}</td>
                <td>{{ $m->instrument->name ?? '-' }}</td>
                <td>{{ $m->position->name ?? 'Anggota' }}</td>
                <td style="text-align: center;">
                    <span class="badge {{ $m->status === 'active' ? 'active' : '' }}">{{ $m->status }}</span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Dicetak pada: {{ date('d F Y, H:i') }} WIB</p>
        <p style="margin-top: 40px; font-weight: bold;">Pengurus Symphony Marching Band</p>
    </div>
</body>
</html>

