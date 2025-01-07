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
        Schema::create('champs', function (Blueprint $table) {
            $table->id(); // Crea una columna 'id' autoincremental
            $table->string('name'); // Crea una columna 'nombre' de tipo string
            $table->string('title'); // Crea una columna 'edad' de tipo entero
            $table->string('tags'); // Crea una columna 'edad' de tipo entero
            $table->timestamps(); // Crea columnas 'created_at' y 'updated_at'
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('champs');
    }
};
