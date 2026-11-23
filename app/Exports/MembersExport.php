<?php

namespace App\Exports;

use App\Models\Member;
use Illuminate\Support\Enumerable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MembersExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping, WithStyles, WithTitle
{
    /**
     * @var array<string, mixed>
     */
    protected array $filters;

    /**
     * @param  array<string, mixed>  $filters
     */
    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    /**
     * Get the collection to export.
     */
    public function collection(): Enumerable
    {
        $query = Member::with(['division', 'instrument', 'position', 'organizationalPeriod'])
            ->when(isset($this->filters['search']) && $this->filters['search'], function ($q) {
                $q->where(function ($q2) {
                    $q2->where('full_name', 'like', '%'.$this->filters['search'].'%')
                        ->orWhere('member_code', 'like', '%'.$this->filters['search'].'%');
                });
            })
            ->when(isset($this->filters['division_id']) && $this->filters['division_id'], function ($q) {
                $q->where('division_id', $this->filters['division_id']);
            })
            ->when(isset($this->filters['status']) && $this->filters['status'], function ($q) {
                $q->where('status', $this->filters['status']);
            })
            ->when(isset($this->filters['class']) && $this->filters['class'], function ($q) {
                $q->where('class', 'like', '%'.$this->filters['class'].'%');
            })
            ->orderBy('full_name');

        return $query->get();
    }

    /**
     * Map each row for export.
     *
     * @param  Member  $member
     * @return array<int, mixed>
     */
    public function map($member): array
    {
        static $no = 0;
        $no++;

        return [
            $no,
            $member->member_code,
            $member->full_name,
            $member->gender === 'male' ? 'Laki-laki' : 'Perempuan',
            $member->class ?? '-',
            $member->phone ?? '-',
            $member->division?->name ?? '-',
            $member->instrument?->name ?? '-',
            $member->position?->name ?? '-',
            $member->organizationalPeriod?->name ?? '-',
            match ($member->status) {
                'active' => 'Aktif',
                'inactive' => 'Tidak Aktif',
                'alumni' => 'Alumni',
                default => $member->status,
            },
            $member->enrollment_date?->format('d/m/Y') ?? '-',
        ];
    }

    /**
     * Get the headings for the export.
     *
     * @return array<int, string>
     */
    public function headings(): array
    {
        return [
            'No',
            'Kode Anggota',
            'Nama Lengkap',
            'Jenis Kelamin',
            'Kelas',
            'Telepon',
            'Divisi',
            'Instrumen',
            'Jabatan',
            'Periode',
            'Status',
            'Tanggal Bergabung',
        ];
    }

    /**
     * Style the worksheet.
     */
    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => ['fillType' => 'solid', 'startColor' => ['argb' => 'FF101D3B']],
                'alignment' => ['horizontal' => 'center'],
            ],
        ];
    }

    /**
     * Get the title for the sheet.
     */
    public function title(): string
    {
        return 'Data Anggota';
    }
}
