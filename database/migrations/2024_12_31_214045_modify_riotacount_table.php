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
        Schema::table('riot_accounts', function (Blueprint $table) {
            if (!Schema::hasColumn('riot_accounts', 'summonerid')) {
                $table->integer('summonerid')->nullable();
            }
        });
    }

    public function down()
    {
        Schema::table('riot_accounts', function (Blueprint $table) {         
            if (!Schema::hasColumn('riot_accounts', 'summonerid')) {
                $table->integer('summonerid')->nullable();
            }
        });
    }
};