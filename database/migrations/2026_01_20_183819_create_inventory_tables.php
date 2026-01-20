<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // 1. Inventory Items (Kursi, Marker, Paper)
        Schema::create('inventory_items', function (Blueprint $table) {
            $table->id();
            $table->string('item_name');
            $table->string('category'); // e.g. Furniture, Stationery
            $table->integer('total_quantity')->default(0);
            $table->decimal('unit_price', 10, 2)->default(0);
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // 2. Stock Transactions (Aaya/Gaya)
        Schema::create('inventory_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventory_item_id')->constrained('inventory_items')->onDelete('cascade');
            $table->enum('type', ['purchase', 'usage', 'damage']); // In or Out
            $table->integer('quantity');
            $table->date('date');
            $table->foreignId('performed_by')->constrained('users'); // Kon staff use kar raha hai
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_transactions');
        Schema::dropIfExists('inventory_items');
    }
};