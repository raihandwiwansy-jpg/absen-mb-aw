<?php

use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
});

test('the root returns welcome page with schedule and dress code', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
    $response->assertSee('SYMPHONY');
    $response->assertSee('HITUNG MUNDUR MENUJU LATIHAN');
});

test('login page returns a successful response', function () {
    $response = $this->get('/login');

    $response->assertStatus(200);
});
