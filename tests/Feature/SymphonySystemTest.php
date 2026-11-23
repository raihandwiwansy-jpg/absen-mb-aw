<?php

use App\Models\Attendance;
use App\Models\AttendanceSession;
use App\Models\Member;
use App\Models\PracticeSchedule;
use App\Models\User;
use App\Services\AttendanceService;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
});

test('super admin can login and view dashboard with real statistics', function () {
    $admin = User::where('email', 'admin@symphony.com')->first();
    expect($admin)->not->toBeNull();

    $response = $this->actingAs($admin)->get('/dashboard');
    $response->assertStatus(200);
    $response->assertSee('Symphony Marching Band');
    $response->assertSee('Total Anggota');
});

test('member can login and view dedicated member portal', function () {
    $memberUser = User::where('email', 'ahmad@symphony.com')->first();
    expect($memberUser)->not->toBeNull();

    $response = $this->actingAs($memberUser)->get('/portal/dashboard');
    $response->assertStatus(200);
    $response->assertSee('Portal Anggota');
    $response->assertSee('Ahmad Fauzi');
});

test('members list is accessible to administrator', function () {
    $admin = User::where('email', 'administrator@symphony.com')->first();

    $response = $this->actingAs($admin)->get('/members');
    $response->assertStatus(200);
    $response->assertSee('Manajemen Anggota');
});

test('attendance session creates qr token and allows member to scan', function () {
    $admin = User::where('email', 'admin@symphony.com')->first();
    $schedule = PracticeSchedule::first();
    expect($schedule)->not->toBeNull();

    $attendanceService = app(AttendanceService::class);

    $session = AttendanceSession::create([
        'practice_schedule_id' => $schedule->id,
        'opened_by' => $admin->id,
        'opened_at' => now(),
        'is_active' => true,
    ]);

    $token = $attendanceService->generateToken($session);
    expect($token)->not->toBeNull();

    $memberUser = User::where('email', 'ahmad@symphony.com')->first();
    $member = $memberUser->member;
    expect($member)->not->toBeNull();

    // Act as member and send token to processScan
    $response = $this->actingAs($memberUser)->postJson('/attendance/scan/process', [
        'token' => $token->token,
    ]);

    $response->assertStatus(200);
    $response->assertJson([
        'success' => true,
    ]);

    // Check attendance record created in database
    $attendance = Attendance::where('attendance_session_id', $session->id)
        ->where('member_id', $member->id)
        ->first();

    expect($attendance)->not->toBeNull();
    expect($attendance->status)->toBeIn(['present', 'late']);
});

test('attendance session live-data endpoint returns real-time stats and check-ins', function () {
    $admin = User::where('email', 'admin@symphony.com')->first();
    $schedule = PracticeSchedule::first();

    $session = AttendanceSession::create([
        'practice_schedule_id' => $schedule->id,
        'opened_by' => $admin->id,
        'opened_at' => now(),
        'is_active' => true,
    ]);

    app(AttendanceService::class)->generateToken($session);
    app(AttendanceService::class)->initializeSessionAttendances($session);

    $response = $this->actingAs($admin)->getJson("/attendance/sessions/{$session->id}/live-data");
    $response->assertStatus(200);
    $response->assertJsonStructure([
        'is_active',
        'stats' => ['total_members', 'present', 'late', 'permission', 'sick', 'absent'],
        'recent_check_ins',
        'token_expires_at',
    ]);
});

test('bulk mark endpoint updates all absent members to present asynchronously', function () {
    $admin = User::where('email', 'admin@symphony.com')->first();
    $schedule = PracticeSchedule::first();

    $session = AttendanceSession::create([
        'practice_schedule_id' => $schedule->id,
        'opened_by' => $admin->id,
        'opened_at' => now(),
        'is_active' => true,
    ]);

    app(AttendanceService::class)->generateToken($session);
    app(AttendanceService::class)->initializeSessionAttendances($session);

    $response = $this->actingAs($admin)->postJson("/attendance/sessions/{$session->id}/bulk-mark", [
        'status' => 'present',
        'target' => 'all_absent',
    ]);

    $response->assertStatus(200);
    $response->assertJson([
        'success' => true,
    ]);

    expect($session->attendances()->where('status', 'present')->count())->toBeGreaterThan(0);
});

test('manual attendance returns instant json response with updated statistics', function () {
    $admin = User::where('email', 'admin@symphony.com')->first();
    $schedule = PracticeSchedule::first();
    $member = Member::first();

    $session = AttendanceSession::create([
        'practice_schedule_id' => $schedule->id,
        'opened_by' => $admin->id,
        'opened_at' => now(),
        'is_active' => true,
    ]);

    app(AttendanceService::class)->initializeSessionAttendances($session);

    $response = $this->actingAs($admin)->postJson('/attendance/manual', [
        'attendance_session_id' => $session->id,
        'member_id' => $member->id,
        'status' => 'present',
    ]);

    $response->assertStatus(200);
    $response->assertJson([
        'success' => true,
        'status_label' => 'Hadir',
    ]);
    $response->assertJsonStructure([
        'stats' => ['total_members', 'present', 'late', 'permission', 'sick', 'absent'],
    ]);
});
