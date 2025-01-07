<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
{
    Schema::create('riot_accounts', function (Blueprint $table) {
        $table->id();
        $table->string('puuid')->unique();
        $table->string('game_name');
        $table->string('tag_line');
        $table->string("division"); // Agrega la columna 'division' para string
        $table->string("rango"); // Agrega la columna 'Rango' para string
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('riot_accounts');
    }
};
