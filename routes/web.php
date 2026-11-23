<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AttendanceSessionController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DivisionController;
use App\Http\Controllers\InstrumentController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\MemberPortalController;
use App\Http\Controllers\PermissionRequestController;
use App\Http\Controllers\PracticeScheduleController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WelcomeController;
use App\Models\User;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - Symphony Marching Band Management System
|--------------------------------------------------------------------------
*/

// Public Welcome & Landing Page
Route::get('/', [WelcomeController::class, 'index'])->name('welcome');

// Authentication routes (Guest)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Profile Edit (All authenticated users)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Dedicated Member Portal (All members can view their personal portal)
    Route::prefix('portal')->name('portal.')->group(function () {
        Route::get('/dashboard', [MemberPortalController::class, 'dashboard'])->name('dashboard');
        Route::get('/profile', [MemberPortalController::class, 'profile'])->name('profile');
        Route::get('/attendance', [MemberPortalController::class, 'attendance'])->name('attendance');
        Route::get('/schedules', [MemberPortalController::class, 'schedules'])->name('schedules');
    });

    // Calendar JSON Data (Accessible to all authenticated users for interactive calendar)
    Route::get('schedules/calendar/data', [PracticeScheduleController::class, 'calendar'])->name('schedules.calendar');

    // QR Code Scanner & Recording (Members scan QR)
    Route::get('attendance/scan', [AttendanceController::class, 'scan'])->name('attendance.scan');
    Route::post('attendance/scan/process', [AttendanceController::class, 'processScan'])->name('attendance.process');

    // Permission and Sickness Requests (Members submit requests, staff reviews)
    Route::resource('permission-requests', PermissionRequestController::class);

    // ============================================================
    // ADMINISTRATIVE & STAFF-ONLY ROUTES (Admin, Officer, Coach)
    // Regular members are strictly restricted from accessing these.
    // ============================================================
    Route::middleware('staff')->group(function () {
        // Main Dashboard (Admin, Coach, Officer)
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Member Management (Add, Edit, Import, Delete members)
        Route::get('members/import/template', [MemberController::class, 'downloadTemplate'])->name('members.import.template');
        Route::get('members/export/excel', [MemberController::class, 'exportExcel'])->name('members.export.excel');
        Route::get('members/export/pdf', [MemberController::class, 'exportPdf'])->name('members.export.pdf');
        Route::post('members/import', [MemberController::class, 'importExcel'])->name('members.import');
        Route::resource('members', MemberController::class);

        // Division & Instrument Management
        Route::resource('divisions', DivisionController::class);
        Route::resource('instruments', InstrumentController::class);

        // Practice Schedules Management
        Route::resource('schedules', PracticeScheduleController::class);

        // Attendance Management System (Sessions, Live Monitor, Bulk Mark, Refresh QR)
        Route::get('attendance', [AttendanceController::class, 'index'])->name('attendance.index');
        Route::get('attendance/sessions/{session}/live-data', [AttendanceSessionController::class, 'liveData'])->name('attendance.sessions.live-data');
        Route::post('attendance/sessions/{session}/bulk-mark', [AttendanceSessionController::class, 'bulkMark'])->name('attendance.sessions.bulk-mark');
        Route::post('attendance/sessions/{session}/close', [AttendanceSessionController::class, 'close'])->name('attendance.sessions.close');
        Route::post('attendance/sessions/{session}/refresh-token', [AttendanceSessionController::class, 'refreshToken'])->name('attendance.sessions.refresh');
        Route::get('attendance/sessions/{session}/qr', [AttendanceSessionController::class, 'qrCode'])->name('attendance.sessions.qr');
        Route::resource('attendance/sessions', AttendanceSessionController::class, ['as' => 'attendance'])->except(['edit', 'update']);
        Route::post('attendance/manual', [AttendanceController::class, 'manualRecord'])->name('attendance.manual');
        Route::get('attendance/history', [AttendanceController::class, 'history'])->name('attendance.history');

        // Permission and Sickness Request Approvals
        Route::post('permission-requests/{permissionRequest}/approve', [PermissionRequestController::class, 'approve'])->name('permission-requests.approve');
        Route::post('permission-requests/{permissionRequest}/reject', [PermissionRequestController::class, 'reject'])->name('permission-requests.reject');

        // Reports and Analytics
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/', [ReportController::class, 'index'])->name('index');
            Route::get('/attendance', [ReportController::class, 'attendance'])->name('attendance');
            Route::get('/discipline', [ReportController::class, 'discipline'])->name('discipline');
            Route::get('/division', [ReportController::class, 'division'])->name('division');
            Route::get('/export/pdf', [ReportController::class, 'exportPdf'])->name('export.pdf');
            Route::get('/export/excel', [ReportController::class, 'exportExcel'])->name('export.excel');
        });

        // User & Account Management (Admin only)
        Route::resource('users', UserController::class);

        // Organization & System Settings
        Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
        Route::post('/settings/periods', [SettingsController::class, 'storePeriod'])->name('settings.periods.store');
        Route::post('/settings/periods/{period}/set-active', [SettingsController::class, 'setActivePeriod'])->name('settings.periods.set-active');
    });
});
