<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // 1. Books Inventory
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('author')->nullable();
            $table->string('isbn')->nullable(); // Barcode
            $table->string('category'); // e.g. Science, Fiction
            $table->integer('quantity')->default(1);
            $table->decimal('price', 10, 2)->nullable(); // Lost book fine ke liye
            $table->string('rack_no')->nullable(); // Location
            $table->timestamps();
        });

        // 2. Book Issues (Len Den)
        Schema::create('book_issues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('book_id')->constrained('books')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->date('issue_date');
            $table->date('return_date'); // Expected return
            $table->date('returned_on')->nullable(); // Actual return
            $table->decimal('fine_amount', 8, 2)->default(0);
            $table->enum('status', ['issued', 'returned', 'lost'])->default('issued');
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('book_issues');
        Schema::dropIfExists('books');
    }
};