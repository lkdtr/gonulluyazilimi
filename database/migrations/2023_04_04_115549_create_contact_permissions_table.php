<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContactPermissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contact_permissions', function (Blueprint $table) {
            $table->id();

            $table->integer('user_id')->default("0");

            $table->string('value')->nullable();
            $table->enum('value_type', ['email', 'phone_number']);

            $table->boolean('verified')->default(false);
            $table->string('verification_code')->nullable();
            $table->integer('status')->default("1");

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
        Schema::dropIfExists('contact_permissions');
    }
}
