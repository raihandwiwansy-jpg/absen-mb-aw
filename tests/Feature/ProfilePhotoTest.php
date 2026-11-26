<?php

use App\Models\Member;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
    Storage::fake('public');
});

test('member can upload profile photo and update email, phone, and password', function () {
    $memberUser = User::where('email', 'ahmad@symphony.com')->firstOrFail();
    $member = $memberUser->member;
    expect($member)->not->toBeNull();

    $file = UploadedFile::fake()->image('member-avatar.jpg', 200, 200);

    $response = $this->actingAs($memberUser)->put(route('profile.update'), [
        'name' => 'Ahmad Fauzi Updated',
        'email' => 'ahmad.baru@symphony.com',
        'phone' => '081299998888',
        'photo' => $file,
        'current_password' => 'password',
        'password' => 'newpassword123',
        'password_confirmation' => 'newpassword123',
    ]);

    $response->assertSessionHasNoErrors();
    $response->assertSessionHas('success');

    $memberUser->refresh();
    $member->refresh();

    expect($memberUser->name)->toBe('Ahmad Fauzi Updated');
    expect($memberUser->email)->toBe('ahmad.baru@symphony.com');
    expect($memberUser->phone)->toBe('081299998888');
    expect($member->phone)->toBe('081299998888');
    expect($memberUser->plain_password)->toBe('newpassword123');
    expect(Hash::check('newpassword123', $memberUser->password))->toBeTrue();

    expect($memberUser->photo)->not->toBeNull();
    expect($member->photo)->toBe($memberUser->photo);
    Storage::disk('public')->assertExists($memberUser->photo);

    // Portal profile and member edit page display the uploaded photo
    $this->actingAs($memberUser)
        ->get(route('portal.profile'))
        ->assertStatus(200)
        ->assertSee($member->photo);

    // Admin sees the member's photo in Data Anggota (index & show)
    $admin = User::where('email', 'admin@symphony.com')->firstOrFail();
    $this->actingAs($admin)
        ->get(route('members.index'))
        ->assertStatus(200)
        ->assertSee($member->photo);

    $this->actingAs($admin)
        ->get(route('members.show', $member))
        ->assertStatus(200)
        ->assertSee($member->photo);
});

test('staff roles without member record can upload and display profile photo', function (string $email) {
    $staffUser = User::where('email', $email)->firstOrFail();
    $file = UploadedFile::fake()->image('staff-avatar.png', 200, 200);

    $response = $this->actingAs($staffUser)->put(route('profile.update'), [
        'name' => $staffUser->name,
        'email' => $staffUser->email,
        'phone' => '081377776666',
        'photo' => $file,
    ]);

    $response->assertSessionHasNoErrors();
    $staffUser->refresh();

    expect($staffUser->photo)->not->toBeNull();
    expect($staffUser->phone)->toBe('081377776666');
    expect($staffUser->photo_url)->toContain($staffUser->photo);
    Storage::disk('public')->assertExists($staffUser->photo);

    $this->actingAs($staffUser)
        ->get(route('dashboard'))
        ->assertStatus(200)
        ->assertSee($staffUser->photo);
})->with([
    'super_admin' => ['admin@symphony.com'],
    'administrator' => ['administrator@symphony.com'],
    'coach' => ['pelatih@symphony.com'],
    'attendance_officer' => ['officer@symphony.com'],
]);

test('user can remove their uploaded profile photo', function () {
    $memberUser = User::where('email', 'ahmad@symphony.com')->firstOrFail();
    $member = $memberUser->member;
    $file = UploadedFile::fake()->image('temp-avatar.jpg', 200, 200);

    $this->actingAs($memberUser)->put(route('profile.update'), [
        'name' => $memberUser->name,
        'email' => $memberUser->email,
        'phone' => $member->phone,
        'photo' => $file,
    ])->assertSessionHasNoErrors();

    $memberUser->refresh();
    $oldPhoto = $memberUser->photo;
    expect($oldPhoto)->not->toBeNull();

    $this->actingAs($memberUser)->put(route('profile.update'), [
        'name' => $memberUser->name,
        'email' => $memberUser->email,
        'phone' => $member->phone,
        'remove_photo' => '1',
    ])->assertSessionHasNoErrors();

    $memberUser->refresh();
    $member->refresh();

    expect($memberUser->photo)->toBeNull();
    expect($member->photo)->toBeNull();
    expect($memberUser->photo_url)->toBeNull();
    Storage::disk('public')->assertMissing($oldPhoto);
});

test('user can upload photo larger than 2mb and webp format', function () {
    $memberUser = User::where('email', 'ahmad@symphony.com')->firstOrFail();
    $largeImage = UploadedFile::fake()->image('kamera-hp.webp', 800, 800)->size(4096);

    $response = $this->actingAs($memberUser)->put(route('profile.update'), [
        'name' => $memberUser->name,
        'email' => $memberUser->email,
        'phone' => $memberUser->effective_phone,
        'photo' => $largeImage,
    ]);

    $response->assertSessionHasNoErrors();
    $memberUser->refresh();
    expect($memberUser->photo)->not->toBeNull();
    Storage::disk('public')->assertExists($memberUser->photo);
});

test('admin updating member preserves user link and syncs photo', function () {
    $admin = User::where('email', 'admin@symphony.com')->firstOrFail();
    $memberUser = User::where('email', 'ahmad@symphony.com')->firstOrFail();
    $member = $memberUser->member;
    $file = UploadedFile::fake()->image('admin-upload.jpg', 200, 200);

    $response = $this->actingAs($admin)->put(route('members.update', $member), [
        'full_name' => $member->full_name,
        'gender' => $member->gender,
        'class' => $member->class,
        'phone' => '085511112222',
        'address' => $member->address,
        'enrollment_date' => $member->enrollment_date->format('Y-m-d'),
        'division_id' => $member->division_id,
        'instrument_id' => $member->instrument_id,
        'position_id' => $member->position_id,
        'organizational_period_id' => $member->organizational_period_id,
        'status' => $member->status,
        'photo' => $file,
    ]);

    $response->assertSessionHasNoErrors();
    $member->refresh();
    $memberUser->refresh();

    expect($member->user_id)->toBe($memberUser->id);
    expect($member->photo)->not->toBeNull();
    expect($memberUser->photo)->toBe($member->photo);
    expect($memberUser->phone)->toBe('085511112222');
    Storage::disk('public')->assertExists($member->photo);
});
