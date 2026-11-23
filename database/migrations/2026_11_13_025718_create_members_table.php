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
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->string('member_code')->unique();
            $table->foreignId('user_id')->nullable()->nullOnDelete()->constrained('users');
            $table->string('full_name');
            $table->enum('gender', ['male', 'female']);
            $table->string('class')->nullable();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('photo')->nullable();
            $table->date('enrollment_date');
            $table->foreignId('division_id')->nullable()->nullOnDelete()->constrained('divisions');
            $table->foreignId('instrument_id')->nullable()->nullOnDelete()->constrained('instruments');
            $table->foreignId('position_id')->nullable()->nullOnDelete()->constrained('positions');
            $table->foreignId('organizational_period_id')->nullable()->nullOnDelete()->constrained('organizational_periods');
            $table->enum('status', ['active', 'inactive', 'alumni'])->default('active');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};
