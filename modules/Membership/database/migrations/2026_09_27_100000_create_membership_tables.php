<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // A contact's membership of the association. The member number is
        // unique within the installation and is the key imports match on.
        Schema::create('memberships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contact_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('number', 20)->nullable()->unique();
            // applicant, active, suspended, left, rejected
            $table->string('status', 20)->default('active');
            $table->date('applied_at')->nullable();
            $table->date('joined_at')->nullable();
            $table->date('left_at')->nullable();
            // Registered in DERBİS (the state's association information system).
            $table->boolean('derbis_registered')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('status');
        });

        // Membership history shown to the member and the management.
        Schema::create('membership_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('membership_id')->constrained()->cascadeOnDelete();
            $table->string('type', 30);
            $table->date('occurred_on');
            // "Ek üyelik süresi" of the source system, in months.
            $table->integer('extra_months')->nullable();
            $table->text('note')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();

            $table->index(['membership_id', 'occurred_on']);
        });

        $this->backfill();
    }

    /**
     * Existing members become membership records: accounts with a member
     * number (users.lkd_user_id, the same number as the former membership
     * system) and contacts holding the member affiliation without one.
     */
    private function backfill(): void
    {
        $memberType = DB::table('affiliation_types')->where('key', 'member')->value('id');
        $now = now();

        $affiliationStart = fn (int $contactId) => $memberType
            ? DB::table('contact_affiliations')->where('contact_id', $contactId)->where('affiliation_type_id', $memberType)->orderBy('started_at')->value('started_at')
            : null;

        $create = function (int $contactId, ?string $number, ?string $joinedAt) use ($now) {
            $membershipId = DB::table('memberships')->insertGetId([
                'contact_id' => $contactId, 'number' => $number, 'status' => 'active',
                'joined_at' => $joinedAt, 'created_at' => $now, 'updated_at' => $now,
            ]);
            DB::table('membership_events')->insert([
                'membership_id' => $membershipId, 'type' => 'joined', 'occurred_on' => $joinedAt ?? $now->toDateString(),
                'note' => 'Önceki kayıtlardan aktarıldı', 'created_at' => $now, 'updated_at' => $now,
            ]);
        };

        DB::table('users')->where('lkd_user_id', '>', 0)->whereNotNull('contact_id')->orderBy('id')
            ->get(['contact_id', 'lkd_user_id', 'created_at'])
            ->each(function ($user) use ($create, $affiliationStart) {
                if (DB::table('memberships')->where('contact_id', $user->contact_id)->orWhere('number', (string) $user->lkd_user_id)->exists()) {
                    return;
                }
                $create($user->contact_id, (string) $user->lkd_user_id, $affiliationStart($user->contact_id) ?? substr((string) $user->created_at, 0, 10));
            });

        if ($memberType) {
            DB::table('contact_affiliations')->where('affiliation_type_id', $memberType)
                ->where(fn ($query) => $query->whereNull('ended_at')->orWhere('ended_at', '>', now()->toDateString()))
                ->whereNotIn('contact_id', DB::table('memberships')->select('contact_id'))
                ->orderBy('id')->get(['contact_id', 'started_at'])
                ->unique('contact_id')
                ->each(fn ($affiliation) => $create($affiliation->contact_id, null, $affiliation->started_at));
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('membership_events');
        Schema::dropIfExists('memberships');
    }
};
