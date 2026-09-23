<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('seminar_offers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('seminar_subject_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('seminar_subject_proposal_id')->nullable()->constrained()->nullOnDelete();
            $table->text('summary');
            $table->string('target_audience');
            $table->string('seminar_type');
            $table->unsignedSmallInteger('duration');
            $table->date('availability_start_date')->nullable();
            $table->date('availability_end_date')->nullable();
            $table->text('cities')->nullable();
            $table->text('technical_requirements')->nullable();
            $table->text('biography');
            $table->text('reference_links')->nullable();
            $table->text('notes')->nullable();
            $table->string('status')->default('pending');
            $table->timestamps();
            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void { Schema::dropIfExists('seminar_offers'); }
};
