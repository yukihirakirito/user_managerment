<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->string('student_code')->unique();
            $table->string('major')->nullable();
            $table->date('enrollment_date')->nullable();
            $table->date('graduation_date')->nullable();
            $table->decimal('gpa', 3, 2)->nullable();
            $table->timestamps();

            $table->index('student_code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};