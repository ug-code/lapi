<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('fund_yields', function (Blueprint $table) {
            $table->id();
            $table->string('fund_id');
            $table->decimal('yield_value', 10, 2);
            $table->dateTime('date');
            $table->json('raw_data');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('fund_yields');
    }
}; 