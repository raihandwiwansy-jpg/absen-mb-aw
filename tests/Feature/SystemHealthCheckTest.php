<?php

use App\Models\PracticeSchedule;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
});

test('all administrative views render without errors', function () {
    $admin = User::where('email', 'admin@symphony.com')->first();
    expect($admin)->not->toBeNull();

    $routes = [
        '/dashboard',
        '/members',
        '/members/create',
        '/members/import/template',
        '/members/export/excel',
        '/members/export/pdf',
        '/divisions',
        '/instruments',
        '/schedules',
        '/schedules/create',
        '/attendance',
        '/attendance/sessions',
        '/attendance/scan',
        '/permission-requests',
        '/reports',
        '/reports/attendance',
        '/reports/discipline',
        '/reports/division',
        '/users',
        '/settings',
    ];

    foreach ($routes as $route) {
        $response = $this->actingAs($admin)->get($route);
        expect($response->getStatusCode())->toBeIn([200, 302], "Route {$route} failed with status {$response->getStatusCode()}");
    }
});

test('schedule detail view renders without officer null error', function () {
    $admin = User::where('email', 'admin@symphony.com')->first();
    $schedule = PracticeSchedule::first();
    expect($schedule)->not->toBeNull();

    // Ensure schedule with no officer renders cleanly
    $schedule->update(['officer_id' => null]);

    $response = $this->actingAs($admin)->get("/schedules/{$schedule->id}");
    $response->assertStatus(200);
    $response->assertSee('Belum Ditentukan');
});

test('login view renders responsive marching band showcase and no test demo credentials for public release', function () {
    $response = $this->get('/login');
    $response->assertStatus(200);
    $response->assertSee('SYMPHONY');
    $response->assertSee('Brass Section');
    $response->assertSee('Battery Percussion');
    $response->assertSee('Color Guard');
    $response->assertDontSee('Akun Uji Coba Cepat');
    $response->assertDontSee('👑 Super Admin');
});

test('member portal views render without errors', function () {
    $memberUser = User::where('email', 'ahmad@symphony.com')->first();
    expect($memberUser)->not->toBeNull();

    $routes = [
        '/portal/dashboard',
        '/portal/profile',
        '/portal/attendance',
        '/portal/schedules',
    ];

    foreach ($routes as $route) {
        $response = $this->actingAs($memberUser)->get($route);
        expect($response->status())->toBe(200, "Portal route {$route} failed with status {$response->status()}");
    }
});

test('regular member cannot access administrative routes and is redirected to portal dashboard', function () {
    $memberUser = User::where('email', 'ahmad@symphony.com')->first();
    expect($memberUser)->not->toBeNull();

    $protectedRoutes = [
        '/dashboard',
        '/members',
        '/members/create',
        '/divisions',
        '/instruments',
        '/schedules',
        '/attendance',
        '/attendance/sessions',
        '/reports',
        '/users',
        '/settings',
    ];

    foreach ($protectedRoutes as $route) {
        $response = $this->actingAs($memberUser)->get($route);
        $response->assertRedirect(route('portal.dashboard'));
    }
});
