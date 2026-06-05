<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('lecture_documents')) {
            return;
        }

        Schema::create('lecture_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lecture_id')
                ->constrained('lectures')
                ->cascadeOnDelete();
            $table->string('title');
            $table->string('file_path');
            $table->string('file_type', 20);
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['lecture_id', 'is_active', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lecture_documents');
    }
};

