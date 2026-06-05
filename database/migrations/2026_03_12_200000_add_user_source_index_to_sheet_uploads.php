<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add composite index for "latest upload per user per source" dashboard queries.
     * Example: SheetUpload::where('user_id', $id)->where('data_source_link_id', $sourceId)->latest()->first()
     */
    public function up(): void
    {
        Schema::table('sheet_uploads', function (Blueprint $table) {
            $table->index(['user_id', 'data_source_link_id'], 'sheet_uploads_user_id_data_source_link_id_index');
        });
    }

    public function down(): void
    {
        Schema::table('sheet_uploads', function (Blueprint $table) {
            $table->dropIndex('sheet_uploads_user_id_data_source_link_id_index');
        });
    }
};
