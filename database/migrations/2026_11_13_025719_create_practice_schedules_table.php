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
        Schema::create('practice_schedules', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->enum('type', ['regular', 'additional', 'competition'])->default('regular');
            $table->date('date');
            $table->time('start_time');
            $table->time('end_time');
            $table->string('location');
            $table->text('description')->nullable();
            $table->foreignId('officer_id')->nullable()->nullOnDelete()->constrained('users');
            $table->foreignId('organizational_period_id')->nullable()->nullOnDelete()->constrained('organizational_periods');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('practice_schedules');
    }
};
