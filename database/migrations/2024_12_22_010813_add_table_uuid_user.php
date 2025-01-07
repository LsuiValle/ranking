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
        Schema::table('users', function (Blueprint $table) {
            $table->string("puuid"); // Agrega la columna 'deleted_at' para soft deletes
            $table->string("division"); // Agrega la columna 'division' para string
            $table->string("rango"); // Agrega la columna 'Rango' para string
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string("puuid"); // Agrega la columna 'deleted_at' para soft deletes
            $table->string("division"); // Agrega la columna 'division' para string
            $table->string("rango"); // Agrega la columna 'Rango' para string
        });
    }
};
