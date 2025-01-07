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
        Schema::create('historial', function (Blueprint $table) {
            $table->id(); // Primary Key
            $table->unsignedBigInteger('id_user'); // Clave foránea para usuarios
            $table->unsignedBigInteger('id_champion'); // Clave foránea para campeones
            $table->boolean('result'); // Columna de resultado (boolean)
            $table->json('detalle_historial'); // Columna de detalle en formato JSON
            $table->dateTime('fecha_partida', precision: 0); // Columna de detalle en formato JSON
            $table->timestamps(); // Columna de timestamps (created_at, updated_at)

            // Claves foráneas
            $table->foreign('id_user')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('id_champion')->references('id')->on('champs')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('historial');
    }
};