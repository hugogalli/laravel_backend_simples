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
        Schema::create('atendimentos', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('description');
            $table->string('pessoa');
            $table->string('status');
            $table->timestamps();

            $table->unsignedBigInteger('user_id'); // Chave estrangeira para o usuário
            $table->foreign('user_id')->references('id')->on('users');

            $table->unsignedBigInteger('cliente_id'); // Chave estrangeira para os clientes
            $table->foreign('cliente_id')->references('id')->on('clientes');

            $table->unsignedBigInteger('area_id'); // Chave estrangeira para o usuário
            $table->foreign('area_id')->references('id')->on('areas');

            $table->unsignedBigInteger('analista_id')->nullable(); // Chave estrangeira para o analista
            $table->foreign('analista_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('atendimentos');
    }
};
