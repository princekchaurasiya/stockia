<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sheet_uploads', function (Blueprint $table) {
            $table->date('report_date')->nullable()->after('row_count');
        });
    }

    public function down(): void
    {
        Schema::table('sheet_uploads', function (Blueprint $table) {
            $table->dropColumn('report_date');
        });
    }
};
