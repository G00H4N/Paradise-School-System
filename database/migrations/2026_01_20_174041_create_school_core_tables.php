<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // 1. School Classes (e.g. Nursery, Prep)
        Schema::create('school_classes', function (Blueprint $table) {
            $table->id();
            $table->string('class_name');
            $table->string('section_name')->nullable();
            $table->integer('numeric_name')->default(0); // Sorting ke liye
            $table->timestamps();
        });

        // 2. Transport Routes (Spec Module 6)
        Schema::create('transport_routes', function (Blueprint $table) {
            $table->id();
            $table->string('route_title'); // e.g. "City Center"
            $table->string('vehicle_number')->nullable();
            $table->string('driver_name')->nullable();
            $table->string('driver_phone')->nullable();
            $table->decimal('fare_amount', 10, 2)->default(0); // Route Income
            $table->timestamps();
        });

        // 3. Fee Types (e.g. Monthly Fee, Exam Fee)
        Schema::create('fee_types', function (Blueprint $table) {
            $table->id();
            $table->string('fee_title');
            $table->decimal('default_amount', 10, 2);
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // 4. Expense Categories (e.g. Utility Bills, Rent)
        Schema::create('expense_categories', function (Blueprint $table) {
            $table->id();
            $table->string('category_name');
            $table->timestamps();
        });

        // Seed default categories
        DB::table('expense_categories')->insert([
            ['category_name' => 'Utility Bills'],
            ['category_name' => 'Rent'],
            ['category_name' => 'Maintenance']
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('expense_categories');
        Schema::dropIfExists('fee_types');
        Schema::dropIfExists('transport_routes');
        Schema::dropIfExists('school_classes');
    }
};