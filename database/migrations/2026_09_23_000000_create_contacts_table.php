<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->string('type', 20)->default('person');
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('organization_name')->nullable();
            // TC kimlik no for a person, tax number for an organization.
            $table->string('identity_number', 20)->nullable()->index();
            $table->string('email')->nullable()->index();
            $table->string('phone', 30)->nullable();
            $table->date('birthday')->nullable();
            $table->unsignedBigInteger('city_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('contact_id')->nullable()->unique()->after('id')->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('contact_id');
        });

        Schema::dropIfExists('contacts');
    }
};
