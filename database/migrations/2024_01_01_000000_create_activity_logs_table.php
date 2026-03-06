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
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            
            // User relationship
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->cascadeOnDelete();
            
            // Activity details
            $table->string('action'); // created, updated, deleted, login, logout, etc
            $table->string('model')->nullable(); // Model name (User, Student, etc)
            $table->unsignedBigInteger('model_id')->nullable(); // Record ID
            
            // Changes tracking
            $table->json('changes')->nullable(); // JSON data of changes
            
            // Request details
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            
            // Timestamps
            $table->timestamps();
            
            // Indexes for performance
            $table->index('user_id');
            $table->index('action');
            $table->index('model');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};