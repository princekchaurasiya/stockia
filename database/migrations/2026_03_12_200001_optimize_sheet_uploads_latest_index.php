<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Replace two-column index with three-column index including created_at
     * so that "latest upload per user per source" queries (ORDER BY created_at DESC)
     * can use the index and avoid filesort.
     *
     * Best index: (user_id, data_source_link_id, created_at)
     * Query: SheetUpload::where('user_id', $id)->where('data_source_link_id', $sourceId)->latest()->first()
     */
    public function up(): void
    {
        Schema::table('sheet_uploads', function (Blueprint $table) {
            // Add new index first so FKs (which use leftmost prefix) still have an index
            $table->index(
                ['user_id', 'data_source_link_id', 'created_at'],
                'idx_sheet_uploads_user_source_date'
            );
            $table->dropIndex('sheet_uploads_user_id_data_source_link_id_index');
        });
    }

    public function down(): void
    {
        Schema::table('sheet_uploads', function (Blueprint $table) {
            $table->dropIndex('idx_sheet_uploads_user_source_date');
            $table->index(
                ['user_id', 'data_source_link_id'],
                'sheet_uploads_user_id_data_source_link_id_index'
            );
        });
    }
};
