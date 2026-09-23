<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('seminar_requests', function (Blueprint $table) {
            $table->foreignId('organization_id')->nullable()->after('seminar_subject_id')
                ->constrained('organizations')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('seminar_requests', function (Blueprint $table) {
            $table->dropConstrainedForeignId('organization_id');
        });
    }
};
