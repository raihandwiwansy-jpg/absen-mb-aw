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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attendance_session_id')->constrained('attendance_sessions')->cascadeOnDelete();
            $table->foreignId('member_id')->constrained('members')->cascadeOnDelete();
            $table->enum('status', ['present', 'permission', 'sick', 'absent', 'late'])->default('absent');
            $table->dateTime('check_in_time')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('recorded_by')->nullable()->nullOnDelete()->constrained('users');
            $table->timestamps();

            $table->unique(['attendance_session_id', 'member_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
