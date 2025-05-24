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
            $table->integer('categories_id')->nullable();
            $table->string('code')->nullable();
            $table->string('management_company_id')->nullable();
            $table->string('title')->nullable();
            $table->string('type')->nullable();
            $table->boolean('tefas')->default(false);
            $table->float('yield_1m')->nullable();
            $table->float('yield_3m')->nullable();
            $table->float('yield_6m')->nullable();
            $table->float('yield_ytd')->nullable();
            $table->float('yield_1y')->nullable();
            $table->float('yield_3y')->nullable();
            $table->float('yield_5y')->nullable();
            $table->dateTime('expires_at')->nullable();

            // İndeksler
            $table->index('fund_id');
            $table->index('code');
            $table->index('management_company_id');
            $table->index('expires_at');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('fund_yields');
    }
};
