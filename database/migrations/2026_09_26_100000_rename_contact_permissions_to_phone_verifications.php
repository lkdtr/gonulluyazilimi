<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // The table only ever held phone verification codes; communication
    // consents now live in consent_events.
    public function up(): void
    {
        Schema::rename('contact_permissions', 'phone_verifications');
    }

    public function down(): void
    {
        Schema::rename('phone_verifications', 'contact_permissions');
    }
};
