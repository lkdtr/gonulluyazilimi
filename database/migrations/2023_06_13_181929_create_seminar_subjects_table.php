<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSeminarSubjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('seminar_subjects', function (Blueprint $table) {
            $table->id();
            $table->string('subject');
            $table->text('summary')->nullable();
            $table->longText('syllabus')->nullable();
            $table->integer('duration')->default("1");

            $table->integer('status')->default("1");

            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
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
        Schema::dropIfExists('seminar_subjects');
    }
}
