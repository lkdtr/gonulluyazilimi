<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Carry over today's rule: an active account with an LKD member number
     * is a member, any other active account is a volunteer. Contacts that
     * already have the affiliation are skipped.
     */
    public function up(): void
    {
        $types = DB::table('affiliation_types')->whereIn('key', ['member', 'volunteer'])->pluck('id', 'key');

        DB::table('users')->where('status', 1)->whereNotNull('contact_id')->chunkById(500, function ($users) use ($types) {
            $rows = [];

            foreach ($users as $user) {
                $typeId = $user->lkd_user_id > 0 ? $types['member'] : $types['volunteer'];

                $exists = DB::table('contact_affiliations')
                    ->where('contact_id', $user->contact_id)
                    ->where('affiliation_type_id', $typeId)
                    ->exists();

                if (! $exists) {
                    $rows[] = [
                        'contact_id' => $user->contact_id,
                        'affiliation_type_id' => $typeId,
                        'started_at' => $user->created_at ? substr($user->created_at, 0, 10) : null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }

            DB::table('contact_affiliations')->insert($rows);
        });
    }

    public function down(): void
    {
        // The table itself is dropped by the previous migration.
    }
};
