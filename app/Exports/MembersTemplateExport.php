<?php

namespace App\Exports;

use App\Models\Division;
use App\Models\Instrument;
use App\Models\Position;
use Maatwebsite\Excel\Concerns\Export;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MembersTemplateExport implements Export, WithMultipleSheets
{
    /**
     * @return array<int, mixed>
     */
    public function sheets(): array
    {
        return [
            new MembersDataTemplateSheet,
            new MembersReferenceSheet,
        ];
    }
}

/**
 * Sheet 1: Data Anggota (Template utama untuk diisi)
 */
class MembersDataTemplateSheet implements FromArray, ShouldAutoSize, WithHeadings, WithStyles, WithTitle
{
    public function title(): string
    {
        return 'Data Anggota';
    }

    /**
     * @return array<int, string>
     */
    public function headings(): array
    {
        return [
            'nama_lengkap',
            'email',
            'jenis_kelamin',
            'kelas',
            'telepon',
            'alamat',
            'divisi',
            'instrumen',
            'jabatan',
            'status',
            'tanggal_bergabung',
            'kode_anggota',
        ];
    }

    /**
     * @return array<int, array<int, string>>
     */
    public function array(): array
    {
        return [
            [
                'Dimas Pratama',
                'dimas.pratama@symphony.com',
                'Laki-laki',
                'XI TKJ 1',
                '081234567891',
                'Jl. Sudirman No. 10, Perdagangan',
                'Brass Section',
                'Trumpet',
                'Anggota',
                'Aktif',
                '2026-01-15',
                '',
            ],
            [
                'Nadia Putri',
                'nadia.putri@symphony.com',
                'Perempuan',
                'X RPL 2',
                '081234567892',
                'Jl. Melati No. 5, Perdagangan',
                'Percussion Section',
                'Snare Drum',
                'Anggota',
                'Aktif',
                '2026-01-15',
                '',
            ],
            [
                'Rizky Kurniawan',
                'rizky.kurniawan@symphony.com',
                'Laki-laki',
                'XI AKL 1',
                '081234567893',
                'Jl. Kartini No. 8, Perdagangan',
                'Woodwind Section',
                'Clarinet',
                'Anggota',
                'Aktif',
                '2026-01-15',
                '',
            ],
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        // Style Header Row (Navy Blue, White Bold, Centered)
        $sheet->getRowDimension(1)->setRowHeight(28);

        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['argb' => 'FFFFFFFF'],
                    'size' => 11,
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF101D3B'], // Symphony Navy
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],
            'A1:L4' => [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => 'FFD1D5DB'],
                    ],
                ],
            ],
        ];
    }
}

/**
 * Sheet 2: Panduan & Referensi Master Data
 */
class MembersReferenceSheet implements FromArray, ShouldAutoSize, WithStyles, WithTitle
{
    public function title(): string
    {
        return 'Panduan & Referensi';
    }

    /**
     * @return array<int, array<int, string>>
     */
    public function array(): array
    {
        $divisions = Division::orderBy('name')->pluck('name')->toArray();
        $instruments = Instrument::with('division')->orderBy('name')->get()->map(function ($inst) {
            return $inst->name.' (Divisi: '.($inst->division?->name ?? '-').')';
        })->toArray();
        $positions = Position::orderBy('name')->pluck('name')->toArray();

        $rows = [
            ['PANDUAN PENGISIAN TEMPLATE IMPORT ANGGOTA SYMPHONY', ''],
            ['Symphony Marching Band - SMK Al Wasliyah Perdagangan', ''],
            ['', ''],
            ['NAMA KOLOM', 'PENJELASAN & CONTOH NILAI'],
            ['nama_lengkap', 'WAJIB. Nama lengkap anggota (Contoh: Ahmad Fauzi)'],
            ['email', 'OPSIONAL. Email unik. Jika diisi, akun portal dibuatkan otomatis (password: password)'],
            ['jenis_kelamin', "WAJIB. Diisi 'Laki-laki' / 'L' atau 'Perempuan' / 'P'"],
            ['kelas', 'OPSIONAL. Kelas siswa di sekolah (Contoh: X RPL 1, XI TKJ 2)'],
            ['telepon', 'OPSIONAL. Nomor WhatsApp / telepon aktif (Contoh: 081234567890)'],
            ['alamat', 'OPSIONAL. Alamat tempat tinggal anggota (Contoh: Jl. Merdeka No. 5)'],
            ['divisi', 'OPSIONAL. Nama divisi marching band (Lihat daftar divisi resmi di bawah)'],
            ['instrumen', 'OPSIONAL. Nama instrumen musik yang dimainkan (Lihat daftar instrumen di bawah)'],
            ['jabatan', 'OPSIONAL. Jabatan anggota (Contoh: Anggota, Ketua Divisi, dll. Default: Anggota)'],
            ['status', "OPSIONAL. 'Aktif', 'Tidak Aktif', atau 'Alumni' (Default: Aktif)"],
            ['tanggal_bergabung', 'OPSIONAL. Format YYYY-MM-DD (Contoh: 2026-01-15, default: hari ini)'],
            ['kode_anggota', 'OPSIONAL. Boleh dikosongkan! Sistem otomatis membuat kode unik format SMB-2026-XXX'],
            ['', ''],
            ['DAFTAR DIVISI RESMI TERDAFTAR DI SISTEM', ''],
        ];

        foreach ($divisions as $idx => $div) {
            $rows[] = [($idx + 1).'. '.$div, 'Divisi aktif'];
        }

        $rows[] = ['', ''];
        $rows[] = ['DAFTAR INSTRUMEN RESMI TERDAFTAR DI SISTEM', ''];

        foreach ($instruments as $idx => $inst) {
            $rows[] = [($idx + 1).'. '.$inst, 'Instrumen terdaftar'];
        }

        $rows[] = ['', ''];
        $rows[] = ['DAFTAR JABATAN RESMI', ''];

        foreach ($positions as $idx => $pos) {
            $rows[] = [($idx + 1).'. '.$pos, 'Jabatan organisasi'];
        }

        return $rows;
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 13, 'color' => ['argb' => 'FF101D3B']],
            ],
            2 => [
                'font' => ['italic' => true, 'size' => 10, 'color' => ['argb' => 'FFC6A044']],
            ],
            4 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF101D3B'],
                ],
            ],
            'A18' => [
                'font' => ['bold' => true, 'size' => 11, 'color' => ['argb' => 'FF101D3B']],
            ],
        ];
    }
}
