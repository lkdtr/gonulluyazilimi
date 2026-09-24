<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Every grant or withdrawal of a communication consent (email, SMS,
        // WhatsApp, phone). The latest event per channel is the consent in
        // force; the history is the proof.
        Schema::create('consent_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contact_id')->constrained()->cascadeOnDelete();
            $table->string('channel', 20);
            $table->boolean('granted');
            // profile, register, admin, import...
            $table->string('source', 30);
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('ip', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamp('created_at');

            $table->index(['contact_id', 'channel', 'id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consent_events');
    }
};
