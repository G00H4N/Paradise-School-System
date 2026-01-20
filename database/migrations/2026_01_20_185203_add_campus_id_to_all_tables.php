<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // In tables mein campus_id add kar rahe hain
        $tables = ['users', 'students', 'fee_invoices', 'expenses', 'staff_details', 'inventory_items', 'vehicles'];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->foreignId('campus_id')->nullable()->after('id')->constrained('campuses')->onDelete('cascade');
                });
            }
        }
    }

    public function down(): void
    {
        $tables = ['users', 'students', 'fee_invoices', 'expenses', 'staff_details', 'inventory_items', 'vehicles'];
        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->dropForeign(['campus_id']);
                    $table->dropColumn('campus_id');
                });
            }
        }
    }
};