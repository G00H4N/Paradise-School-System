<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('study_materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')->constrained('school_classes')->onDelete('cascade');
            $table->foreignId('subject_id')->nullable()->constrained('subjects')->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('file_path')->nullable(); // PDF/Doc
            $table->string('youtube_url')->nullable(); // Video Link
            $table->foreignId('uploaded_by')->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('study_materials');
    }
};