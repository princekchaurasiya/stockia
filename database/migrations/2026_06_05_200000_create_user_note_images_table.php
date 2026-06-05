<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('user_note_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_note_id')->constrained('user_notes')->cascadeOnDelete();
            $table->string('file_path');
            $table->string('original_name')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['user_note_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_note_images');
    }
};
