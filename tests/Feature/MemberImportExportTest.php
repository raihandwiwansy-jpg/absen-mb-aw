<?php

use App\Exports\MembersTemplateExport;
use App\Models\Member;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
});

test('administrator can download member import excel template', function () {
    $admin = User::where('email', 'admin@symphony.com')->first();

    $response = $this->actingAs($admin)->get('/members/import/template');

    $response->assertStatus(200);
    $response->assertHeader('content-disposition');
    expect($response->headers->get('content-disposition'))->toContain('template-import-anggota-symphony');
});

test('administrator can download member import csv template', function () {
    $admin = User::where('email', 'admin@symphony.com')->first();

    $response = $this->actingAs($admin)->get('/members/import/template?format=csv');

    $response->assertStatus(200);
    expect($response->headers->get('content-disposition'))->toContain('.csv');
});

test('attendance officer can see import excel button and download template', function () {
    $officer = User::where('email', 'officer@symphony.com')->first();
    expect($officer)->not->toBeNull();

    $response = $this->actingAs($officer)->get('/members');
    $response->assertStatus(200);
    $response->assertSee('Import Excel / CSV');
    $response->assertSee('Tambah Anggota');

    $templateResponse = $this->actingAs($officer)->get('/members/import/template');
    $templateResponse->assertStatus(200);
    expect($templateResponse->headers->get('content-disposition'))->toContain('template-import-anggota-symphony');
});

test('administrator can export members to excel', function () {
    $admin = User::where('email', 'admin@symphony.com')->first();

    $response = $this->actingAs($admin)->get('/members/export/excel');

    $response->assertStatus(200);
    expect($response->headers->get('content-disposition'))->toContain('.xlsx');
});

test('administrator can successfully import members from excel file', function () {
    $admin = User::where('email', 'admin@symphony.com')->first();

    // Store template to storage disk
    Storage::fake('local');
    Excel::store(new MembersTemplateExport, 'test_import.xlsx', 'local');

    $filePath = Storage::disk('local')->path('test_import.xlsx');
    $uploadedFile = new UploadedFile(
        $filePath,
        'test_import.xlsx',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        null,
        true
    );

    $response = $this->actingAs($admin)->post('/members/import', [
        'file' => $uploadedFile,
    ]);

    $response->assertRedirect('/members');
    $response->assertSessionHas('success');

    // Verify imported member exists in database
    $member = Member::where('full_name', 'Dimas Pratama')->first();
    expect($member)->not->toBeNull();
    expect($member->gender)->toBe('male');
    expect($member->class)->toBe('XI TKJ 1');
    expect($member->member_code)->toStartWith('SMB-');
    expect($member->division?->name)->toBe('Brass Section');
    expect($member->instrument?->name)->toBe('Trumpet');

    // Check second member
    $memberNadia = Member::where('full_name', 'Nadia Putri')->first();
    expect($memberNadia)->not->toBeNull();
    expect($memberNadia->gender)->toBe('female');
    expect($memberNadia->division?->name)->toBe('Percussion Section');
    expect($memberNadia->instrument?->name)->toBe('Snare Drum');
});
