<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // 1. Users Table (Updated with CNIC, Role & Two-Factor)
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email', 191)->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();

            // Custom Columns (Spec Requirement)
            $table->string('cnic', 50)->nullable()->unique(); // Family Logic
            $table->string('phone')->nullable(); // SMS Alerts
            $table->enum('role', ['admin', 'teacher', 'parent', 'student', 'accountant', 'librarian'])->default('student');
            $table->tinyInteger('status')->default(1); // 1=Active

            // Jetstream & Two-Factor Columns (Merged here)
            $table->string('profile_photo_path', 2048)->nullable();
            $table->text('two_factor_secret')->nullable();
            $table->text('two_factor_recovery_codes')->nullable();
            $table->timestamp('two_factor_confirmed_at')->nullable();

            $table->timestamps();
        });

        // 2. Password Reset Tokens
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email', 191)->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        // 3. Sessions
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id', 191)->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};