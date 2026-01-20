<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // 1. Students Table (Spec Module 1)
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Student Login
            $table->foreignId('parent_id')->nullable()->constrained('users'); // Family Logic Link
            $table->foreignId('class_id')->constrained('school_classes');
            $table->foreignId('transport_route_id')->nullable()->constrained('transport_routes'); // Module 6

            $table->string('admission_no')->unique();
            $table->string('roll_no')->nullable();
            $table->string('full_name');
            $table->string('father_name');
            $table->string('mother_name')->nullable();
            $table->enum('gender', ['Male', 'Female']);
            $table->date('birthday');
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->date('admission_date');

            // Extra Spec Fields from Video 2
            $table->string('caste')->nullable();
            $table->string('blood_group')->nullable();
            $table->string('religion')->default('Islam');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. Staff Details (Spec Module 4)
        Schema::create('staff_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('designation'); // Teacher, Accountant
            $table->decimal('basic_salary', 10, 2);
            $table->date('joining_date');
            $table->string('qualification')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 3. Admission Inquiries (Leads Management)
        Schema::create('admission_inquiries', function (Blueprint $table) {
            $table->id();
            $table->string('student_name');
            $table->string('father_name');
            $table->string('phone');
            $table->foreignId('class_id')->constrained('school_classes');
            $table->string('previous_school')->nullable();
            $table->enum('status', ['pending', 'converted', 'rejected'])->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admission_inquiries');
        Schema::dropIfExists('staff_details');
        Schema::dropIfExists('students');
    }
};