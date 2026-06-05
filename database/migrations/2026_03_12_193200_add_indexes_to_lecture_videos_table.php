<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('lecture_videos')) {
            return;
        }

        Schema::table('lecture_videos', function (Blueprint $table) {
            $indexes = collect(Schema::getIndexes('lecture_videos'))->pluck('name');
            if (! $indexes->contains('lecture_videos_lecture_active_sort_index')) {
                $table->index(['lecture_id', 'is_active', 'sort_order'], 'lecture_videos_lecture_active_sort_index');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lecture_videos', function (Blueprint $table) {
            $table->dropIndex('lecture_videos_lecture_active_sort_index');
        });
    }
};
