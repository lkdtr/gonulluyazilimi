<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seminar_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('seminar_subject_id')->constrained()->restrictOnDelete();
            $table->string('organization');
            $table->string('location');
            $table->date('seminar_date');
            $table->string('status')->default('pending');
            $table->timestamps();

            $table->index(['status', 'seminar_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seminar_requests');
    }
};
