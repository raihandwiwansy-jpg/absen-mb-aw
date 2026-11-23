<?php

namespace App\Imports;

use App\Models\Division;
use App\Models\Instrument;
use App\Models\Member;
use App\Models\OrganizationalPeriod;
use App\Models\Position;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class MembersImport implements SkipsEmptyRows, ToCollection, WithHeadingRow
{
    /** @var array<int, string> */
    private array $errors = [];

    private int $imported = 0;

    /** @var array<int, string> */
    private array $usedCodesInBatch = [];

    /**
     * Process the imported collection.
     *
     * @param  Collection<int, Collection<string, mixed>>  $rows
     */
    public function collection(Collection $rows): void
    {
        $activePeriod = OrganizationalPeriod::where('is_active', true)->first();
        $defaultPosition = Position::where('name', 'like', '%Anggota%')->first();

        foreach ($rows as $index => $row) {
            $rowNum = $index + 2; // Accounting for 1-based index and heading row

            try {
                // Read full name with fallback column names
                $name = trim($row['nama_lengkap'] ?? $row['nama'] ?? $row['full_name'] ?? '');

                // Skip if name is completely empty or heading artifact
                if (empty($name)) {
                    continue;
                }

                // Skip header duplicates or instructions accidentally included
                if (str_starts_with(strtolower($name), 'contoh:') || strtolower($name) === 'nama lengkap') {
                    continue;
                }

                // 1. Division matching
                $divisionId = null;
                $rawDivision = trim($row['divisi'] ?? $row['division'] ?? '');
                if (! empty($rawDivision)) {
                    $division = Division::where('name', 'like', '%'.$rawDivision.'%')->first();
                    $divisionId = $division?->id;
                }

                // 2. Instrument matching
                $instrumentId = null;
                $rawInstrument = trim($row['instrumen'] ?? $row['instrument'] ?? '');
                if (! empty($rawInstrument)) {
                    $instrument = Instrument::where('name', 'like', '%'.$rawInstrument.'%')->first();
                    $instrumentId = $instrument?->id;
                    if (! $divisionId && $instrument?->division_id) {
                        $divisionId = $instrument->division_id;
                    }
                }

                // 3. Position matching (defaults to 'Anggota')
                $positionId = null;
                $rawPosition = trim($row['jabatan'] ?? $row['position'] ?? '');
                if (! empty($rawPosition)) {
                    $position = Position::where('name', 'like', '%'.$rawPosition.'%')->first();
                    $positionId = $position?->id;
                }
                if (! $positionId && $defaultPosition) {
                    $positionId = $defaultPosition->id;
                }

                // 4. Organizational Period matching (defaults to active period)
                $periodId = null;
                $rawPeriod = trim($row['periode'] ?? $row['period'] ?? '');
                if (! empty($rawPeriod)) {
                    $period = OrganizationalPeriod::where('name', 'like', '%'.$rawPeriod.'%')->first();
                    $periodId = $period?->id;
                }
                if (! $periodId && $activePeriod) {
                    $periodId = $activePeriod->id;
                }

                // 5. Gender normalization
                $rawGender = strtolower(trim($row['jenis_kelamin'] ?? $row['gender'] ?? $row['jk'] ?? ''));
                $gender = in_array($rawGender, ['p', 'perempuan', 'wanita', 'female', 'f'], true) ? 'female' : 'male';

                // 6. Status normalization
                $rawStatus = strtolower(trim($row['status'] ?? ''));
                $status = match ($rawStatus) {
                    'inactive', 'tidak aktif', 'nonaktif', 'non-aktif' => 'inactive',
                    'alumni', 'lulus' => 'alumni',
                    default => 'active',
                };

                // 7. Enrollment date parsing (handles Excel serial numbers and string dates)
                $rawDate = $row['tanggal_bergabung'] ?? $row['enrollment_date'] ?? null;
                $enrollmentDate = now()->format('Y-m-d');
                if (! empty($rawDate)) {
                    if (is_numeric($rawDate)) {
                        try {
                            $enrollmentDate = ExcelDate::excelToDateTimeObject((float) $rawDate)->format('Y-m-d');
                        } catch (\Throwable) {
                            $enrollmentDate = now()->format('Y-m-d');
                        }
                    } else {
                        try {
                            $enrollmentDate = Carbon::parse($rawDate)->format('Y-m-d');
                        } catch (\Throwable) {
                            $enrollmentDate = now()->format('Y-m-d');
                        }
                    }
                }

                // 8. User Account Creation & Linking (if valid email provided)
                $userId = null;
                $rawEmail = strtolower(trim($row['email'] ?? $row['surel'] ?? $row['user_email'] ?? ''));
                if (! empty($rawEmail) && filter_var($rawEmail, FILTER_VALIDATE_EMAIL)) {
                    $user = User::where('email', $rawEmail)->first();
                    if (! $user) {
                        $user = User::create([
                            'name' => $name,
                            'email' => $rawEmail,
                            'password' => Hash::make('password'),
                            'plain_password' => 'password',
                        ]);
                        $user->assignRole('member');
                    }
                    $userId = $user->id;
                }

                // 9. Member Code resolution & generation (safe batch handling)
                $rawCode = trim($row['kode_anggota'] ?? $row['member_code'] ?? '');
                $existingMember = null;
                if (! empty($rawCode)) {
                    $existingMember = Member::where('member_code', $rawCode)->first();
                } elseif ($userId) {
                    $existingMember = Member::where('user_id', $userId)->first();
                }

                if ($existingMember) {
                    $memberCode = $existingMember->member_code;
                } else {
                    $memberCode = ! empty($rawCode)
                        ? $rawCode
                        : $this->generateMemberCode();
                }

                $this->usedCodesInBatch[] = $memberCode;

                // 10. Persist Member
                $class = trim($row['kelas'] ?? $row['class'] ?? '');
                $phone = trim($row['telepon'] ?? $row['no_hp'] ?? $row['phone'] ?? $row['hp'] ?? '');
                $address = trim($row['alamat'] ?? $row['address'] ?? '');

                Member::updateOrCreate(
                    ['member_code' => $memberCode],
                    [
                        'user_id' => $userId,
                        'full_name' => $name,
                        'gender' => $gender,
                        'class' => ! empty($class) ? $class : null,
                        'phone' => ! empty($phone) ? $phone : null,
                        'address' => ! empty($address) ? $address : null,
                        'division_id' => $divisionId,
                        'instrument_id' => $instrumentId,
                        'position_id' => $positionId,
                        'organizational_period_id' => $periodId,
                        'status' => $status,
                        'enrollment_date' => $enrollmentDate,
                    ]
                );

                $this->imported++;
            } catch (\Throwable $e) {
                $this->errors[] = "Baris {$rowNum}: ".$e->getMessage();
            }
        }
    }

    /**
     * Generate a unique sequential member code in SMB-YYYY-NNN format.
     */
    private function generateMemberCode(): string
    {
        $year = now()->year;
        $count = Member::withTrashed()->whereYear('created_at', $year)->count() + 1 + count($this->usedCodesInBatch);

        do {
            $code = sprintf('SMB-%d-%03d', $year, $count);
            $existsInDb = Member::withTrashed()->where('member_code', $code)->exists();
            $existsInBatch = in_array($code, $this->usedCodesInBatch, true);

            if ($existsInDb || $existsInBatch) {
                $count++;
            }
        } while ($existsInDb || $existsInBatch);

        return $code;
    }

    /**
     * Get import errors.
     *
     * @return array<int, string>
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Get number of imported rows.
     */
    public function getImportedCount(): int
    {
        return $this->imported;
    }
}
