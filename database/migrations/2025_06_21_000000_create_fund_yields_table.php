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
            $table->text('query_params')->nullable();
            $table->json('response_data')->nullable();
            $table->dateTime('expires_at')->nullable();
            // Query parametreleri için indeks
            $table->index('query_params');
            // Süresi dolanları hızlı bulmak için indeks
            $table->index('expires_at');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('fund_yields');
    }
};
