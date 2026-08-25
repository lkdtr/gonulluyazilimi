<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contact_permissions', function (Blueprint $table) {
            $table->timestamp('verification_code_expires_at')->nullable()->after('verification_code');
            $table->unsignedTinyInteger('verification_attempts')->default(0)->after('verification_code_expires_at');
            $table->timestamp('verified_at')->nullable()->after('verified');
            $table->index(['value_type', 'value']);
        });
    }

    public function down(): void
    {
        Schema::table('contact_permissions', function (Blueprint $table) {
            $table->dropIndex(['value_type', 'value']);
            $table->dropColumn(['verification_code_expires_at', 'verification_attempts', 'verified_at']);
        });
    }
};
