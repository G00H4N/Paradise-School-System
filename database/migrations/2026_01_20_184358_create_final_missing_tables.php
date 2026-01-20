<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // 1. Time Table (Class Routine)
        Schema::create('time_tables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')->constrained('school_classes')->onDelete('cascade');
            $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
            $table->foreignId('teacher_id')->constrained('users')->onDelete('cascade'); // Teacher Role wala user
            $table->string('day'); // Monday, Tuesday
            $table->time('start_time');
            $table->time('end_time');
            $table->string('room_no')->nullable();
            $table->timestamps();
        });

        // 2. Leave Applications (Approval System)
        Schema::create('leave_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->date('from_date');
            $table->date('to_date');
            $table->text('reason');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->text('admin_remark')->nullable();
            $table->timestamps();
        });

        // 3. Campuses (Multi-Branch Setup)
        Schema::create('campuses', function (Blueprint $table) {
            $table->id();
            $table->string('campus_name'); // e.g. "Main Branch", "City Campus"
            $table->string('address')->nullable();
            $table->string('contact_number')->nullable();
            $table->string('principal_name')->nullable();
            $table->timestamps();
        });

        // 4. SMS/WhatsApp Logs (History Tracking)
        Schema::create('sms_logs', function (Blueprint $table) {
            $table->id();
            $table->string('receiver_number');
            $table->text('message_body');
            $table->string('type'); // 'SMS' or 'WhatsApp'
            $table->string('status'); // 'Sent', 'Failed'
            $table->foreignId('sent_by')->nullable()->constrained('users'); // Auto or Admin
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sms_logs');
        Schema::dropIfExists('campuses');
        Schema::dropIfExists('leave_applications');
        Schema::dropIfExists('time_tables');
    }
};