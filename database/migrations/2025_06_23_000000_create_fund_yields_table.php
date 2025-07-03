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
            // $table->string('fund_id')->nullable(); // Eğer ihtiyaç varsa ekle
            $table->integer('categories_id')->nullable();
            $table->string('code')->nullable();
            $table->string('management_company_id')->nullable();
            $table->string('title')->nullable();
            $table->string('type')->nullable();
            $table->boolean('tefas')->default(false);

            // Nominal getiriler (float = double precision)
            $table->double('yield_1m')->nullable();
            $table->double('yield_3m')->nullable();
            $table->double('yield_6m')->nullable();
            $table->double('yield_ytd')->nullable();
            $table->double('yield_1y')->nullable();
            $table->double('yield_3y')->nullable();
            $table->double('yield_5y')->nullable();

            // Reel getiriler
            $table->double('yield_1m_reel')->nullable();
            $table->double('yield_3m_reel')->nullable();
            $table->double('yield_6m_reel')->nullable();
            $table->double('yield_ytd_reel')->nullable();
            $table->double('yield_1y_reel')->nullable();
            $table->double('yield_3y_reel')->nullable();
            $table->double('yield_5y_reel')->nullable();

            $table->dateTime('expires_at')->nullable();

            // İndeksler
            // $table->index('fund_id'); // fund_id alanı yoksa bu satırı kaldır
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
