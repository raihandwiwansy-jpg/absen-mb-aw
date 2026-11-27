<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->index('status');
        });

        Schema::table('practice_schedules', function (Blueprint $table) {
            $table->index('date');
            $table->index('type');
        });

        Schema::table('attendance_sessions', function (Blueprint $table) {
            $table->index('is_active');
            $table->index(['practice_schedule_id', 'is_active']);
        });

        Schema::table('attendances', function (Blueprint $table) {
            $table->index('status');
            $table->index(['attendance_session_id', 'status']);
            $table->index(['member_id', 'status']);
        });

        Schema::table('permission_requests', function (Blueprint $table) {
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->dropIndex(['status']);
        });

        Schema::table('practice_schedules', function (Blueprint $table) {
            $table->dropIndex(['date']);
            $table->dropIndex(['type']);
        });

        Schema::table('attendance_sessions', function (Blueprint $table) {
            $table->dropIndex(['is_active']);
            $table->dropIndex(['practice_schedule_id', 'is_active']);
        });

        Schema::table('attendances', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['attendance_session_id', 'status']);
            $table->dropIndex(['member_id', 'status']);
        });

        Schema::table('permission_requests', function (Blueprint $table) {
            $table->dropIndex(['status']);
        });
    }
};
