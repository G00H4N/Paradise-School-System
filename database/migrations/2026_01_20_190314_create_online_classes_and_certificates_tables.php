<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // 1. Online Classes (Video 1 Sidebar)
        Schema::create('online_classes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campus_id')->nullable()->constrained('campuses'); // Multi-campus
            $table->foreignId('class_id')->constrained('school_classes')->onDelete('cascade');
            $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
            $table->foreignId('teacher_id')->constrained('users'); // Teacher assigned
            $table->string('topic');
            $table->string('meeting_platform'); // Zoom, Google Meet
            $table->text('meeting_link');
            $table->string('meeting_id')->nullable();
            $table->string('password')->nullable();
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->enum('status', ['scheduled', 'live', 'ended'])->default('scheduled');
            $table->timestamps();
        });

        // 2. Issued Certificates History (Video 2 @ 07:34)
        Schema::create('generated_certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campus_id')->nullable()->constrained('campuses');
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->string('certificate_type'); // 'character', 'leaving', 'transfer'
            $table->string('serial_number')->unique(); // e.g., CERT-2026-001
            $table->date('issue_date');
            $table->text('remarks')->nullable(); // Conduct remarks
            $table->foreignId('issued_by')->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('generated_certificates');
        Schema::dropIfExists('online_classes');
    }
};