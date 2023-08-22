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
        Schema::table('atendimentos', function (Blueprint $table) {
            $table->string('tipo')->nullable()->default('duvida'); // Defina um valor padrão
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('atendimentos', function (Blueprint $table) {
            $table->dropColumn('tipo');
        });
    }
};
