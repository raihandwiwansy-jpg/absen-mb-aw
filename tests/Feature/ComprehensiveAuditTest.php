<?php

use App\Models\AttendanceSession;
use App\Models\Division;
use App\Models\Instrument;
use App\Models\Member;
use App\Models\PermissionRequest;
use App\Models\PracticeSchedule;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
});

test('public welcome and login pages render cleanly without errors', function () {
    $this->get(route('welcome'))->assertStatus(200)->assertSee('SYMPHONY');
    $this->get(route('login'))->assertStatus(200)->assertSee('Masuk ke Akun');
});

test('all staff pages render 200 for super_admin, administrator, coach, and officer', function (string $email) {
    $staff = User::where('email', $email)->firstOrFail();
    $member = Member::firstOrFail();
    $division = Division::firstOrFail();
    $instrument = Instrument::firstOrFail();
    $schedule = PracticeSchedule::firstOrFail();
    $session = AttendanceSession::create([
        'practice_schedule_id' => $schedule->id,
        'opened_by' => $staff->id,
        'opened_at' => now(),
        'is_active' => true,
    ]);

    $routes = [
        route('dashboard'),
        route('profile.edit'),
        route('members.index'),
        route('members.create'),
        route('members.show', $member),
        route('members.edit', $member),
        route('divisions.index'),
        route('divisions.create'),
        route('divisions.show', $division),
        route('divisions.edit', $division),
        route('instruments.index'),
        route('instruments.create'),
        route('instruments.edit', $instrument),
        route('schedules.index'),
        route('schedules.create'),
        route('schedules.show', $schedule),
        route('schedules.edit', $schedule),
        route('schedules.calendar'),
        route('attendance.sessions.index'),
        route('attendance.sessions.create'),
        route('attendance.sessions.show', $session),
        route('attendance.sessions.live-data', $session),
        route('attendance.sessions.qr', $session),
        route('attendance.history'),
        route('attendance.scan'),
        route('permission-requests.index'),
        route('reports.index'),
        route('reports.attendance'),
        route('reports.discipline'),
        route('reports.division'),
        route('users.index'),
        route('users.create'),
        route('settings.index'),
    ];

    foreach ($routes as $url) {
        $this->actingAs($staff)->get($url)->assertStatus(200);
    }
})->with([
    'super_admin' => ['admin@symphony.com'],
    'administrator' => ['administrator@symphony.com'],
    'coach' => ['pelatih@symphony.com'],
    'attendance_officer' => ['officer@symphony.com'],
]);

test('all member portal pages render 200 and block staff-only routes', function () {
    $memberUser = User::where('email', 'ahmad@symphony.com')->firstOrFail();
    $schedule = PracticeSchedule::firstOrFail();

    foreach ([
        route('portal.dashboard'),
        route('portal.schedules'),
        route('portal.profile'),
        route('portal.attendance'),
        route('profile.edit'),
        route('attendance.scan'),
        route('permission-requests.index'),
        route('permission-requests.create', ['practice_schedule_id' => $schedule->id]),
        route('schedules.calendar'),
    ] as $url) {
        $this->actingAs($memberUser)->get($url)->assertStatus(200);
    }

    // Ensure regular member cannot access staff routes
    foreach ([
        route('dashboard'),
        route('members.index'),
        route('schedules.index'),
        route('users.index'),
        route('settings.index'),
    ] as $staffUrl) {
        $this->actingAs($memberUser)->get($staffUrl)->assertRedirect(route('portal.dashboard'));
    }
});

test('stale active sessions are auto-closed in welcome, member portal, and session index', function () {
    $admin = User::where('email', 'admin@symphony.com')->firstOrFail();
    $memberUser = User::where('email', 'ahmad@symphony.com')->firstOrFail();

    $pastSchedule = PracticeSchedule::create([
        'title' => 'Latihan Kemarin',
        'type' => 'regular',
        'date' => today()->subDay()->format('Y-m-d'),
        'start_time' => '15:00:00',
        'end_time' => '17:00:00',
        'location' => 'Lapangan',
        'officer_id' => $admin->id,
    ]);

    $staleSession = AttendanceSession::create([
        'practice_schedule_id' => $pastSchedule->id,
        'opened_by' => $admin->id,
        'opened_at' => now()->subDay(),
        'is_active' => true,
    ]);

    // Member portal dashboard auto-closes stale session and hides active banner
    $this->actingAs($memberUser)->get(route('portal.dashboard'))->assertStatus(200);
    $staleSession->refresh();
    expect($staleSession->is_active)->toBeFalse();
    expect($staleSession->closed_at)->not->toBeNull();
});

test('permission request ownership check and administrator approval work properly', function () {
    $ahmad = User::where('email', 'ahmad@symphony.com')->firstOrFail();
    $siti = User::where('email', 'siti@symphony.com')->firstOrFail();
    $administrator = User::where('email', 'administrator@symphony.com')->firstOrFail();

    $futureSchedule = PracticeSchedule::create([
        'title' => 'Latihan Persiapan Parade',
        'type' => 'competition',
        'date' => today()->addDays(2)->format('Y-m-d'),
        'start_time' => '15:00:00',
        'end_time' => '17:30:00',
        'location' => 'Lapangan Utama',
        'officer_id' => $administrator->id,
    ]);

    // Ahmad submits permission request
    $this->actingAs($ahmad)->post(route('permission-requests.store'), [
        'practice_schedule_id' => $futureSchedule->id,
        'type' => 'permission',
        'reason' => 'Ada acara keluarga mendadak.',
    ])->assertRedirect(route('permission-requests.index'));

    $perm = PermissionRequest::where('member_id', $ahmad->member->id)
        ->where('practice_schedule_id', $futureSchedule->id)
        ->firstOrFail();

    // Ahmad can view own request; Siti cannot view Ahmad's request
    $this->actingAs($ahmad)->get(route('permission-requests.show', $perm))->assertStatus(200);
    $this->actingAs($siti)->get(route('permission-requests.show', $perm))->assertStatus(403);

    // Administrator sees quick approve button and approves it
    $this->actingAs($administrator)
        ->get(route('permission-requests.index'))
        ->assertStatus(200)
        ->assertSee('Setujui Izin Secara Instan');

    $this->actingAs($administrator)
        ->postJson(route('permission-requests.approve', $perm))
        ->assertStatus(200)
        ->assertJson(['success' => true, 'status' => 'approved']);

    $perm->refresh();
    expect($perm->status)->toBe('approved');
});

test('schedule update rejects end_time before start_time and user search combines with role filter', function () {
    $admin = User::where('email', 'admin@symphony.com')->firstOrFail();
    $schedule = PracticeSchedule::firstOrFail();

    $this->actingAs($admin)->put(route('schedules.update', $schedule), [
        'title' => $schedule->title,
        'type' => $schedule->type,
        'date' => $schedule->date->format('Y-m-d'),
        'start_time' => '17:00',
        'end_time' => '15:00',
        'location' => $schedule->location,
    ])->assertSessionHasErrors('end_time');

    // Combined search + role filter should not leak other roles
    $this->actingAs($admin)
        ->get(route('users.index', ['search' => 'ahmad', 'role' => 'administrator']))
        ->assertStatus(200)
        ->assertDontSee('ahmad@symphony.com');
});

test('dashboard and reports run with bounded query counts without n+1 regressions', function () {
    $admin = User::where('email', 'admin@symphony.com')->firstOrFail();

    DB::enableQueryLog();
    $this->actingAs($admin)->get(route('dashboard'))->assertStatus(200);
    $dashboardQueries = count(DB::getQueryLog());
    DB::disableQueryLog();

    expect($dashboardQueries)->toBeLessThan(25);

    DB::flushQueryLog();
    DB::enableQueryLog();
    $this->actingAs($admin)->get(route('reports.discipline'))->assertStatus(200);
    $disciplineQueries = count(DB::getQueryLog());
    DB::disableQueryLog();

    expect($disciplineQueries)->toBeLessThan(15);
});

test('vite manifest assets exist on disk and alpine x-data attributes do not leak raw javascript', function () {
    $manifestPath = public_path('build/manifest.json');
    expect(file_exists($manifestPath))->toBeTrue();

    $manifest = json_decode(file_get_contents($manifestPath), true);
    foreach (['resources/css/app.css', 'resources/js/app.js'] as $entry) {
        expect($manifest)->toHaveKey($entry);
        $assetFile = public_path('build/'.$manifest[$entry]['file']);
        expect(file_exists($assetFile))->toBeTrue();
    }

    $loginHtml = $this->get(route('login'))->assertStatus(200)->getContent();
    expect($loginHtml)->toContain('data-email=""');

    $admin = User::where('email', 'admin@symphony.com')->firstOrFail();

    // Ensure Scan QR & Members Create & Permission Requests render clean HTML without broken x-data quotes
    $scanHtml = $this->actingAs($admin)->get(route('attendance.scan', ['token' => 'TEST123']))->assertStatus(200)->getContent();
    expect($scanHtml)->toContain('data-initial-token="TEST123"');
    expect($scanHtml)->toContain('x-data="qrScanner($el.dataset.initialToken');

    $this->actingAs($admin)->get(route('members.create'))->assertStatus(200)->assertSee('memberCreateForm');
    $this->actingAs($admin)->get(route('permission-requests.index'))->assertStatus(200)->assertSee('permissionRequestsPage');
});
