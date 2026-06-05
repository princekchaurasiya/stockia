<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lecture_videos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lecture_id')->constrained('lectures')->cascadeOnDelete();
            $table->string('label');
            $table->string('youtube_url');
            $table->string('video_type')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['lecture_id', 'is_active', 'sort_order'], 'lecture_videos_lecture_active_sort_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lecture_videos');
    }
};

