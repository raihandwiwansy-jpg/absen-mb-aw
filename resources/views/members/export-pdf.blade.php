@php
    $isAdmin = auth()->check() && auth()->user()->hasAnyRole(['super_admin', 'administrator']);
@endphp
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Daftar Induk Anggota - Symphony Marching Band</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 9px; color: #333; margin: 15px; }
        .header-table { width: 100%; border-bottom: 3px double #C6A044; padding-bottom: 10px; margin-bottom: 15px; }
        .org-title { font-size: 16px; font-weight: bold; color: #101D3B; text-transform: uppercase; margin: 0; }
        .school-title { font-size: 12px; font-weight: bold; color: #C6A044; letter-spacing: 1px; margin: 2px 0; }
        .meta-info { font-size: 9px; color: #666; }
        .report-header { text-align: center; margin: 15px 0 10px 0; }
        .report-title { font-size: 14px; font-weight: bold; color: #101D3B; text-transform: uppercase; letter-spacing: 0.5px; }
        .report-subtitle { font-size: 9px; color: #666; margin-top: 3px; }
        table.data-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table.data-table th { background-color: #101D3B; color: #ffffff; font-weight: bold; padding: 6px 5px; text-align: left; font-size: 8px; border: 1px solid #101D3B; }
        table.data-table td { padding: 4.5px 5px; border: 1px solid #e5e7eb; font-size: 8px; }
        table.data-table tr:nth-child(even) { background-color: #f9fafb; }
        .badge { display: inline-block; padding: 2px 4px; font-size: 7px; font-weight: bold; border-radius: 3px; text-transform: uppercase; text-align: center; }
        .status-active { background-color: #d1fae5; color: #065f46; }
        .status-inactive { background-color: #f3f4f6; color: #4b5563; }
        .status-alumni { background-color: #dbeafe; color: #1e40af; }
        .footer-table { width: 100%; margin-top: 25px; font-size: 9px; }
    </style>
</head>
<body>

    <table class="header-table">
        <tr>
            <td style="width: 75px; text-align: center; vertical-align: middle;">
                <img src="{{ public_path('logo.jpg') }}" style="width: 60px; height: 60px; border-radius: 50%;" alt="Logo">
            </td>
            <td style="vertical-align: middle; padding-left: 10px;">
                <div class="school-title">SMK AL WASLIYAH PERDAGANGAN</div>
                <div class="org-title">SYMPHONY MARCHING BAND</div>
                <div class="meta-info">Sekretariat: Jl. Besar Perdagangan, Kab. Simalungun • Tahun 2026</div>
                <div class="meta-info">Sistem Informasi Manajemen Presensi & Aktivitas Organisasi</div>
            </td>
        </tr>
    </table>

    <div class="report-header">
        <div class="report-title">DAFTAR INDUK ANGGOTA MARCHING BAND</div>
        <div class="report-subtitle">Waktu Unduh: {{ now()->translatedFormat('d F Y, H:i') }} WIB • Total Data: {{ count($members) }} Anggota</div>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 20px; text-align: center;">No</th>
                <th style="width: 65px;">Kode</th>
                <th>Nama Lengkap</th>
                <th style="width: 24px; text-align: center;">L/P</th>
                <th style="width: 45px;">Kelas</th>
                <th>Divisi</th>
                <th>Instrumen</th>
                <th style="width: 75px;">No. HP/WA</th>
                @if($isAdmin)
                    <th style="width: 110px;">Email Akun</th>
                    <th style="width: 70px;">Kata Sandi (PW)</th>
                @endif
                <th style="width: 45px; text-align: center;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($members as $index => $member)
            <tr>
                <td style="text-align: center;">{{ $index + 1 }}</td>
                <td style="font-family: monospace; font-weight: bold;">{{ $member->member_code }}</td>
                <td><strong>{{ $member->full_name }}</strong></td>
                <td style="text-align: center;">{{ $member->gender === 'male' ? 'L' : 'P' }}</td>
                <td>{{ $member->class ?? '-' }}</td>
                <td>{{ $member->division?->name ?? '-' }}</td>
                <td>{{ $member->instrument?->name ?? '-' }}</td>
                <td style="font-family: monospace;">{{ $member->phone ?? '-' }}</td>
                @if($isAdmin)
                    <td style="font-family: monospace; font-size: 7.5px;">{{ $member->user?->email ?? '-' }}</td>
                    <td style="font-family: monospace; font-weight: bold; color: #101D3B;">{{ $member->user?->plain_password ?? ($member->user ? 'password' : '-') }}</td>
                @endif
                <td style="text-align: center;">
                    <span class="badge status-{{ $member->status }}">
                        {{ match($member->status) {
                            'active' => 'Aktif',
                            'inactive' => 'Non-Aktif',
                            'alumni' => 'Alumni',
                            default => $member->status
                        } }}
                    </span>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="{{ $isAdmin ? 11 : 9 }}" style="text-align: center; padding: 15px; color: #999;">Tidak ada data anggota ditemukan.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <table class="footer-table">
        <tr>
            <td style="width: 70%;"></td>
            <td style="width: 30%; text-align: center;">
                <p>Perdagangan, {{ now()->translatedFormat('d F Y') }}</p>
                <p style="margin-top: 5px; font-weight: bold;">Pembina / Pelatih Marching Band,</p>
                <div style="height: 45px;"></div>
                <p style="font-weight: bold; text-decoration: underline;">Pengurus Symphony Marching Band</p>
                <p style="font-size: 8px; color: #777;">SMK Al Wasliyah Perdagangan</p>
            </td>
        </tr>
    </table>

</body>
</html>

