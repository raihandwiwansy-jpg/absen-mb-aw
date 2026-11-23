<?php

namespace Database\Seeders;

use App\Models\Division;
use App\Models\Instrument;
use App\Models\Member;
use App\Models\OrganizationalPeriod;
use App\Models\Position;
use App\Models\PracticeSchedule;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Clean out dummy transactional data
        Schema::disableForeignKeyConstraints();
        DB::table('attendances')->truncate();
        DB::table('attendance_tokens')->truncate();
        DB::table('attendance_sessions')->truncate();
        DB::table('permission_requests')->truncate();
        DB::table('activity_logs')->truncate();
        DB::table('notifications')->truncate();
        DB::table('members')->truncate();
        DB::table('practice_schedules')->truncate();

        // Remove extra dummy users
        User::whereNotIn('email', [
            'admin@symphony.com',
            'administrator@symphony.com',
            'pelatih@symphony.com',
            'officer@symphony.com',
            'ahmad@symphony.com',
            'siti@symphony.com',
        ])->delete();
        Schema::enableForeignKeyConstraints();

        // 2. Create permissions
        $permissions = [
            'manage-users',
            'manage-members',
            'manage-divisions',
            'manage-schedules',
            'manage-attendance',
            'view-reports',
            'submit-attendance',
            'submit-requests',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // 3. Create roles and assign permissions
        $superAdmin = Role::firstOrCreate(['name' => 'super_admin']);
        $superAdmin->givePermissionTo(Permission::all());

        $administrator = Role::firstOrCreate(['name' => 'administrator']);
        $administrator->givePermissionTo([
            'manage-members',
            'manage-divisions',
            'manage-schedules',
            'manage-attendance',
            'view-reports',
        ]);

        $coach = Role::firstOrCreate(['name' => 'coach']);
        $coach->givePermissionTo(['view-reports']);

        $attendanceOfficer = Role::firstOrCreate(['name' => 'attendance_officer']);
        $attendanceOfficer->givePermissionTo([
            'manage-members',
            'manage-attendance',
            'submit-attendance',
        ]);

        $member = Role::firstOrCreate(['name' => 'member']);
        $member->givePermissionTo([
            'submit-attendance',
            'submit-requests',
        ]);

        // 4. Create Administrative Staff Users
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@symphony.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'plain_password' => 'password',
                'email_verified_at' => now(),
            ]
        );
        $adminUser->syncRoles(['super_admin']);

        $adminUser2 = User::firstOrCreate(
            ['email' => 'administrator@symphony.com'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('password'),
                'plain_password' => 'password',
                'email_verified_at' => now(),
            ]
        );
        $adminUser2->syncRoles(['administrator']);

        $coachUser = User::firstOrCreate(
            ['email' => 'pelatih@symphony.com'],
            [
                'name' => 'Pelatih Utama',
                'password' => Hash::make('password'),
                'plain_password' => 'password',
                'email_verified_at' => now(),
            ]
        );
        $coachUser->syncRoles(['coach']);

        $officerUser = User::firstOrCreate(
            ['email' => 'officer@symphony.com'],
            [
                'name' => 'Petugas Absensi',
                'password' => Hash::make('password'),
                'plain_password' => 'password',
                'email_verified_at' => now(),
            ]
        );
        $officerUser->syncRoles(['attendance_officer']);

        // 5. Create Organizational Period
        $period = OrganizationalPeriod::firstOrCreate(
            ['name' => 'Periode 2025/2026'],
            [
                'start_date' => '2025-07-01',
                'end_date' => '2026-06-30',
                'is_active' => true,
            ]
        );

        // 6. Create Divisions with instruments
        $divisionsData = [
            [
                'name' => 'Brass Section',
                'description' => 'Seksi alat musik tiup logam',
                'color' => '#C6A044',
                'instruments' => ['Trumpet', 'French Horn', 'Trombone', 'Tuba', 'Baritone'],
            ],
            [
                'name' => 'Woodwind Section',
                'description' => 'Seksi alat musik tiup kayu',
                'color' => '#2D6A4F',
                'instruments' => ['Clarinet', 'Flute', 'Saxophone Alto', 'Saxophone Tenor', 'Oboe'],
            ],
            [
                'name' => 'Percussion Section',
                'description' => 'Seksi alat musik perkusi',
                'color' => '#8B2FC9',
                'instruments' => ['Snare Drum', 'Bass Drum', 'Tenor Drum', 'Cymbals', 'Marimba', 'Xylophone'],
            ],
            [
                'name' => 'Color Guard',
                'description' => 'Tim bendera dan visual',
                'color' => '#E63946',
                'instruments' => ['Bendera', 'Senapan', 'Sabre'],
            ],
            [
                'name' => 'Pit Orchestra',
                'description' => 'Orkestra pit pendukung',
                'color' => '#457B9D',
                'instruments' => ['Piano/Keyboard', 'Vibraphone', 'Chimes', 'Glockenspiel'],
            ],
        ];

        $createdDivisions = [];
        $createdInstruments = [];

        foreach ($divisionsData as $divisionData) {
            $instruments = $divisionData['instruments'];
            unset($divisionData['instruments']);

            $division = Division::firstOrCreate(
                ['name' => $divisionData['name']],
                $divisionData
            );
            $createdDivisions[] = $division;

            foreach ($instruments as $instrumentName) {
                $instrument = Instrument::firstOrCreate(
                    ['name' => $instrumentName, 'division_id' => $division->id],
                    ['description' => null]
                );
                $createdInstruments[$division->id][] = $instrument;
            }
        }

        // 7. Create Positions
        $positionsData = [
            'Ketua Umum',
            'Wakil Ketua',
            'Sekretaris',
            'Bendahara',
            'Ketua Divisi',
            'Wakil Ketua Divisi',
            'Anggota',
        ];

        foreach ($positionsData as $positionName) {
            Position::firstOrCreate(['name' => $positionName]);
        }

        $anggotaPosition = Position::where('name', 'Anggota')->first();

        // 8. Create exactly 2 member users: Ahmad Fauzi and Siti Rahayu
        $sampleMembers = [
            [
                'name' => 'Ahmad Fauzi',
                'email' => 'ahmad@symphony.com',
                'gender' => 'male',
                'class' => 'XII RPL 1',
                'division_idx' => 0, // Brass Section
                'instrument_name' => 'Trumpet',
            ],
            [
                'name' => 'Siti Rahayu',
                'email' => 'siti@symphony.com',
                'gender' => 'female',
                'class' => 'XI TKJ 2',
                'division_idx' => 2, // Percussion Section
                'instrument_name' => 'Snare Drum',
            ],
        ];

        foreach ($sampleMembers as $index => $memberData) {
            $memberUser = User::firstOrCreate(
                ['email' => $memberData['email']],
                [
                    'name' => $memberData['name'],
                    'password' => Hash::make('password'),
                    'plain_password' => 'password',
                    'email_verified_at' => now(),
                ]
            );
            $memberUser->syncRoles(['member']);

            $division = $createdDivisions[$memberData['division_idx']];
            $instrument = Instrument::where('name', $memberData['instrument_name'])->where('division_id', $division->id)->first();

            Member::create([
                'user_id' => $memberUser->id,
                'member_code' => 'SMB-2026-'.str_pad($index + 1, 3, '0', STR_PAD_LEFT),
                'full_name' => $memberData['name'],
                'gender' => $memberData['gender'],
                'class' => $memberData['class'],
                'phone' => '08123456789'.($index + 1),
                'address' => 'Perdagangan, Simalungun',
                'enrollment_date' => '2025-07-15',
                'division_id' => $division->id,
                'instrument_id' => $instrument?->id,
                'position_id' => $anggotaPosition?->id,
                'organizational_period_id' => $period->id,
                'status' => 'active',
            ]);
        }

        // 9. Create practice schedules with dress code
        PracticeSchedule::create([
            'title' => 'Latihan Display & Formasi Lapangan',
            'type' => 'regular',
            'date' => now()->addDay()->format('Y-m-d'),
            'start_time' => '14:30:00',
            'end_time' => '17:30:00',
            'location' => 'Lapangan Utama SMK AL Wasliyah Perdagangan',
            'dress_code' => 'Kaos Hitam Symphony & Celana Training Hitam',
            'description' => 'Latihan rutin formasi display & pemantapan nada lagu mars. Wajib membawa instrumen masing-masing dan air minum.',
            'officer_id' => $officerUser->id,
            'organizational_period_id' => $period->id,
        ]);

        PracticeSchedule::create([
            'title' => 'Latihan Sesi Sectional Musik',
            'type' => 'additional',
            'date' => now()->addDays(3)->format('Y-m-d'),
            'start_time' => '08:00:00',
            'end_time' => '11:30:00',
            'location' => 'Ruang Musik & Hall Serbaguna',
            'dress_code' => 'Seragam Olahraga Sekolah',
            'description' => 'Latihan terpisah per divisi (Brass, Percussion, Woodwind, Color Guard).',
            'officer_id' => $officerUser->id,
            'organizational_period_id' => $period->id,
        ]);

        $this->command->info('✅ Database telah dikosongkan dan disiapkan untuk pengujian!');
        $this->command->info('   - Anggota tersisa: 2 orang (Ahmad Fauzi & Siti Rahayu)');
        $this->command->info('   - Presensi & Pengajuan: Bersih (0 data)');
        $this->command->info('   - Jadwal siap uji: 1 jadwal latihan hari ini');
    }
}
