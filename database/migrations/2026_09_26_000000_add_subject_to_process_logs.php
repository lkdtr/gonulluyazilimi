<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Structured audit: which record changed and its old/new values, next
        // to the free-text description the older code writes.
        Schema::table('process_logs', function (Blueprint $table) {
            $table->string('subject_type', 150)->nullable()->after('process');
            $table->unsignedBigInteger('subject_id')->nullable()->after('subject_type');
            $table->json('changes')->nullable()->after('subject_id');

            $table->index(['subject_type', 'subject_id']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::table('process_logs', function (Blueprint $table) {
            $table->dropIndex(['subject_type', 'subject_id']);
            $table->dropIndex(['created_at']);
            $table->dropColumn(['subject_type', 'subject_id', 'changes']);
        });
    }
};
