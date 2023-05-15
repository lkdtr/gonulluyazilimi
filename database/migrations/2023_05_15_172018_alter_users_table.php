<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table){
            if (!Schema::hasColumn('users', 'birthday')) {
                $table->timestamp('birthday')->before("national_id")->nullable();
            }

            if (!Schema::hasColumn('users', 'city_id')) {
                $table->integer('city_id')->before("password")->default(0);
            }

            $table->string('national_id')->unique()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
