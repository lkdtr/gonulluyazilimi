<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('seminar_subject_proposals', function (Blueprint $table) {
            $table->id();
            $table->string('subject');
            $table->string('normalized_subject')->unique();
            $table->string('status')->default('pending');
            $table->foreignId('accepted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void { Schema::dropIfExists('seminar_subject_proposals'); }
};
