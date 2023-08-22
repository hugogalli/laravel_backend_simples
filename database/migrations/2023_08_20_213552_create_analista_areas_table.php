<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('analista_areas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('analista_id');
            $table->unsignedBigInteger('area_id');
            $table->timestamps();

            $table->foreign('analista_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('area_id')->references('id')->on('areas')->onDelete('cascade');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('analista_areas');
    }
};
