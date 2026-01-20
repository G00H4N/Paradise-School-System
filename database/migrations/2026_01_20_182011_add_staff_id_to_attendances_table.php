<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            // Student ID ko nullable karte hain (kyunke kabhi sirf staff ki hazri hogi)
            $table->foreignId('student_id')->nullable()->change();

            // Staff ID add kar rahe hain
            $table->foreignId('staff_id')->nullable()->after('student_id')->constrained('staff_details')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropForeign(['staff_id']);
            $table->dropColumn('staff_id');
            // Wapis student_id ko required karte hain (Rollback par)
            $table->foreignId('student_id')->nullable(false)->change();
        });
    }
};