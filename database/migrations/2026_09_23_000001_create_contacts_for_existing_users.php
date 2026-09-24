<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Give every existing account its contact record. Accounts that already
     * have one are skipped, so the migration can be re-run safely.
     */
    public function up(): void
    {
        DB::table('users')->whereNull('contact_id')->chunkById(500, function ($users) {
            foreach ($users as $user) {
                DB::transaction(function () use ($user) {
                    $contactId = DB::table('contacts')->insertGetId([
                        'type' => 'person',
                        'first_name' => $user->name,
                        'last_name' => $user->surname,
                        'identity_number' => $user->national_id,
                        'email' => $user->email,
                        'phone' => $user->phone_number,
                        'birthday' => $user->birthday,
                        'city_id' => $user->city_id ?: null,
                        'created_at' => $user->created_at,
                        'updated_at' => $user->updated_at,
                    ]);

                    DB::table('users')->where('id', $user->id)->update(['contact_id' => $contactId]);
                });
            }
        });
    }

    public function down(): void
    {
        // The contacts table itself is dropped by the previous migration.
    }
};
