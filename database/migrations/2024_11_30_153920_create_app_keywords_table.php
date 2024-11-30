<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('app_keywords', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->nullable();
            $table->string('eng_keyword')->nullable();
            $table->string('tr_keyword')->nullable();
            $table->boolean('is_learned')->default(false)->nullable();
            $table->string('category')->nullable();
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();
            $table->dateTime('deleted_at')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('app_keywords');
    }
};
