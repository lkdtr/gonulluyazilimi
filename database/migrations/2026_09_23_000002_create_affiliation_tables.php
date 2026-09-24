<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // What a contact can be to the association: volunteer, member, board
        // member... Role and permission templates will hang off these types.
        Schema::create('affiliation_types', function (Blueprint $table) {
            $table->id();
            $table->string('key', 50)->unique();
            $table->string('name');
            $table->text('description')->nullable();
            // Referenced by code; cannot be deleted or re-keyed.
            $table->boolean('is_system')->default(false);
            // Served in terms (boards), so an end date is expected.
            $table->boolean('has_term')->default(false);
            $table->unsignedInteger('sort')->default(0);
            $table->timestamps();
        });

        Schema::create('contact_affiliations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contact_id')->constrained()->cascadeOnDelete();
            $table->foreignId('affiliation_type_id')->constrained()->restrictOnDelete();
            // Position within the type, e.g. Başkan, Sekreter, Asil, Yedek.
            $table->string('title')->nullable();
            $table->date('started_at')->nullable();
            // First day the affiliation no longer holds; null while it lasts.
            $table->date('ended_at')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
            $table->index(['affiliation_type_id', 'ended_at']);
            $table->index(['contact_id', 'affiliation_type_id']);
        });

        $now = now();
        DB::table('affiliation_types')->insert([
            ['key' => 'volunteer', 'name' => 'Gönüllü', 'is_system' => true, 'has_term' => false, 'sort' => 10, 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'member', 'name' => 'Üye', 'is_system' => true, 'has_term' => false, 'sort' => 20, 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'board', 'name' => 'Yönetim Kurulu Üyesi', 'is_system' => false, 'has_term' => true, 'sort' => 30, 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'audit-board', 'name' => 'Denetleme Kurulu Üyesi', 'is_system' => false, 'has_term' => true, 'sort' => 40, 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'discipline-board', 'name' => 'Disiplin Kurulu Üyesi', 'is_system' => false, 'has_term' => true, 'sort' => 50, 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_affiliations');
        Schema::dropIfExists('affiliation_types');
    }
};
