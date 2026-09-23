<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('key', 50)->unique();
            $table->string('name');
            $table->text('description')->nullable();
            // Referenced by code; cannot be deleted or re-keyed.
            $table->boolean('is_system')->default(false);
            $table->timestamps();
        });

        // Permission keys are registered in code (App\Modules\Permissions).
        Schema::create('permission_role', function (Blueprint $table) {
            $table->foreignId('role_id')->constrained()->cascadeOnDelete();
            $table->string('permission', 100);
            $table->primary(['role_id', 'permission']);
        });

        // Roles given to an account directly.
        Schema::create('role_user', function (Blueprint $table) {
            $table->foreignId('role_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->primary(['role_id', 'user_id']);
        });

        // Role templates: holding an affiliation grants these roles.
        Schema::create('affiliation_type_role', function (Blueprint $table) {
            $table->foreignId('affiliation_type_id')->constrained()->cascadeOnDelete();
            $table->foreignId('role_id')->constrained()->cascadeOnDelete();
            $table->primary(['affiliation_type_id', 'role_id']);
        });

        $now = now();
        $owner = DB::table('roles')->insertGetId(['key' => 'owner', 'name' => 'Sahip', 'description' => 'Bütün yetkilere sahiptir.', 'is_system' => true, 'created_at' => $now, 'updated_at' => $now]);
        $manager = DB::table('roles')->insertGetId(['key' => 'manager', 'name' => 'Yönetici', 'description' => 'Yönetim paneline girer, kişi ve kurumları görür.', 'is_system' => true, 'created_at' => $now, 'updated_at' => $now]);

        DB::table('permission_role')->insert([
            ['role_id' => $manager, 'permission' => 'admin.access'],
            ['role_id' => $manager, 'permission' => 'contacts.view'],
        ]);

        // Carry over the legacy users.role levels: 1 owner, 2 manager.
        foreach ([1 => $owner, 2 => $manager] as $level => $roleId) {
            DB::table('users')->where('role', $level)->orderBy('id')->pluck('id')
                ->chunk(500)
                ->each(fn ($ids) => DB::table('role_user')->insert($ids->map(fn ($id) => ['role_id' => $roleId, 'user_id' => $id])->all()));
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('affiliation_type_role');
        Schema::dropIfExists('role_user');
        Schema::dropIfExists('permission_role');
        Schema::dropIfExists('roles');
    }
};
