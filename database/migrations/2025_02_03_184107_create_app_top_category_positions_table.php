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
        Schema::create('app_top_category_positions', function (Blueprint $table) {
            $table->id();
            $table->date('date')->index();
            $table->integer('category_id');
            $table->integer('position');
            $table->timestamps();

            $table->unique(['date', 'category_id'], 'date_category_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('app_top_category_positions');
    }
};
