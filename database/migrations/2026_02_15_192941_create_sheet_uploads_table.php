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
        Schema::create('sheet_uploads', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('original_name');
            $table->string('path');
            $table->json('columns')->nullable()->comment('Detected column headers');
            $table->unsignedInteger('row_count')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sheet_uploads');
    }
};
