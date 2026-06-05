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
        Schema::create('nifty50_extended', function (Blueprint $table) {
            $table->id();
            $table->string('security_symbol')->index();
            $table->string('company_name');
            $table->string('industry');
            $table->decimal('nifty_weightage_pct', 8, 2)->default(0);
            $table->string('sector_thematic_index');
            $table->decimal('sector_thematic_weightage_pct', 8, 2)->default(0);
            $table->string('relationship_of_index')->default('Sector');
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nifty50_extended');
    }
};
