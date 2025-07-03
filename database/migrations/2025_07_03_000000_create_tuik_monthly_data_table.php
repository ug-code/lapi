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
        Schema::create('tuik_monthly_data', function (Blueprint $table) {
            $table->id();
            $table->integer('year');
            $table->unsignedTinyInteger('month'); // 1 - 12 arası
            $table->decimal('value', 10, 2);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tuik_monthly_data');
    }
}
