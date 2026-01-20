<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // 1. Subjects (Dependent on Class)
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')->constrained('school_classes')->onDelete('cascade');
            $table->string('subject_name'); // Mathematics
            $table->string('subject_code')->nullable();
            $table->integer('total_marks')->default(100);
            $table->integer('passing_marks')->default(33);
            $table->timestamps();
        });

        // 2. Exams (Spec Module 5)
        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->string('exam_title'); // Mid Term 2026
            $table->date('start_date');
            $table->string('session_year');
            $table->timestamps();
        });

        // 3. Marks (Dependent on Student, Exam, Subject)
        Schema::create('marks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('exam_id')->constrained('exams')->onDelete('cascade');
            $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
            $table->integer('marks_obtained');
            $table->integer('total_marks');
            $table->string('grade')->nullable(); // A+, B
            $table->text('teacher_comment')->nullable();
            $table->timestamps();
        });

        // 4. Attendance (Spec Module 2: Biometric)
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->date('attendance_date');
            $table->tinyInteger('status')->comment('1=Present, 2=Absent, 3=Leave, 4=Late');
            $table->time('check_in')->nullable(); // Biometric Time Sync
            $table->timestamps();
        });

        // 5. Diaries (Homework - Video 1)
        Schema::create('diaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')->constrained('school_classes');
            $table->foreignId('subject_id')->nullable()->constrained('subjects');
            $table->date('diary_date');
            $table->text('description'); // Homework content
            $table->foreignId('added_by')->constrained('users'); // Teacher
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('diaries');
        Schema::dropIfExists('attendances');
        Schema::dropIfExists('marks');
        Schema::dropIfExists('exams');
        Schema::dropIfExists('subjects');
    }
};