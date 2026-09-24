<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // A person may hold one forwarding per domain, e.g. as a volunteer on
    // penguen.org.tr and as a member on linux.org.tr, both to the same
    // personal address.
    public function up(): void
    {
        Schema::table('email_redirects', function (Blueprint $table) {
            $table->string('domain', 100)->nullable()->after('email_alias');
        });

        DB::table('email_redirects')->orderBy('id')->select(['id', 'email_alias'])->each(function ($redirect) {
            $at = strrpos((string) $redirect->email_alias, '@');
            DB::table('email_redirects')->where('id', $redirect->id)->update([
                'domain' => $at === false ? null : strtolower(substr($redirect->email_alias, $at + 1)),
            ]);
        });

        Schema::table('email_redirects', function (Blueprint $table) {
            $table->dropUnique(['email_forwarding']);
            $table->index('email_forwarding');
            $table->unique(['user_id', 'domain']);
        });
    }

    public function down(): void
    {
        Schema::table('email_redirects', function (Blueprint $table) {
            $table->dropUnique(['user_id', 'domain']);
            $table->dropIndex(['email_forwarding']);
            $table->unique('email_forwarding');
            $table->dropColumn('domain');
        });
    }
};
