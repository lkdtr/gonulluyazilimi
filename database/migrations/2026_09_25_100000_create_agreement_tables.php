<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Agreements people accept (privacy policy, email usage terms...),
        // edited in the admin panel. Code refers to them by key.
        Schema::create('agreements', function (Blueprint $table) {
            $table->id();
            $table->string('key', 50)->unique();
            $table->string('title');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // A published version is never changed: edits go to a draft that is
        // published as the next version, so every acceptance keeps its text.
        Schema::create('agreement_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agreement_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('version');
            $table->longText('content');
            $table->timestamp('published_at')->nullable();
            $table->foreignId('published_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['agreement_id', 'version']);
        });

        Schema::create('agreement_acceptances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agreement_version_id')->constrained()->restrictOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('contact_id')->nullable()->constrained()->nullOnDelete();
            // Where it was accepted: register, email-forwarding, reference...
            $table->string('context', 50)->nullable();
            $table->string('ip', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamp('accepted_at');

            $table->index(['user_id', 'agreement_version_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agreement_acceptances');
        Schema::dropIfExists('agreement_versions');
        Schema::dropIfExists('agreements');
    }
};
