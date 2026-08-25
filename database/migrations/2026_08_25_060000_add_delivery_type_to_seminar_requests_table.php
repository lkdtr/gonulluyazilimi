<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('seminar_requests', function (Blueprint $table) {
            $table->string('seminar_type')->default('in_person')->after('location');
        });
    }

    public function down(): void
    {
        Schema::table('seminar_requests', function (Blueprint $table) {
            $table->dropColumn('seminar_type');
        });
    }
};
