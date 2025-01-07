<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyUsersTable extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'puuid')) {
                $table->string('puuid')->nullable();
            }
            if (!Schema::hasColumn('users', 'division')) {
                $table->string('division')->nullable();
            }
            if (!Schema::hasColumn('users', 'rango')) {
                $table->string('rango')->nullable();
            }
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'puuid')) {
                $table->dropColumn('puuid');
            }
            if (Schema::hasColumn('users', 'division')) {
                $table->dropColumn('division');
            }
            if (Schema::hasColumn('users', 'rango')) {
                $table->dropColumn('rango');
            }
        });
    }
}