<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (! Schema::hasTable('lecture_documents')) {
            return;
        }

        $hasIndex = collect(Schema::getIndexes('lecture_documents'))
            ->contains(fn (array $index) => ($index['name'] ?? '') === 'lecture_documents_lecture_active_sort_index');

        if ($hasIndex) {
            return;
        }

        Schema::table('lecture_documents', function (Blueprint $table) {
            $table->index(['lecture_id', 'is_active', 'sort_order'], 'lecture_documents_lecture_active_sort_index');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('lecture_documents')) {
            return;
        }

        Schema::table('lecture_documents', function (Blueprint $table) {
            $table->dropIndex('lecture_documents_lecture_active_sort_index');
        });
    }
};
