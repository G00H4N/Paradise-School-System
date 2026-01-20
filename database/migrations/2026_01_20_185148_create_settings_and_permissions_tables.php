<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // 1. General Settings (Video 2 @ 35:39)
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('school_name')->default('Paradise Public School');
            $table->string('school_address')->nullable();
            $table->string('school_phone')->nullable();
            $table->string('school_email')->nullable();
            $table->string('currency_symbol')->default('PKR');
            $table->string('current_session')->default('2026-2027'); // Global Session
            $table->string('logo_path')->nullable();
            $table->timestamps();
        });

        // Insert Default Settings immediately
        DB::table('settings')->insert([
            'school_name' => 'Paradise Public Girls Elementary School',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // 2. Permissions Table (Video 2 @ 35:13 - Matrix Logic)
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('module'); // e.g. 'students', 'fees'
            $table->string('action'); // e.g. 'view', 'create', 'edit', 'delete'
            $table->string('slug')->unique(); // e.g. 'students.create'
            $table->timestamps();
        });

        // 3. Role-Permission Pivot (Kon se Role ke paas kya ijazat hai)
        Schema::create('role_permissions', function (Blueprint $table) {
            $table->id();
            $table->string('role'); // 'teacher', 'accountant' (matches User enum)
            $table->foreignId('permission_id')->constrained('permissions')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('role_permissions');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('settings');
    }
};