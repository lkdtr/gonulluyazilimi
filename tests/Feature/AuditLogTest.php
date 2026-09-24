<?php

namespace Tests\Feature;

use App\Models\Contact;
use App\Models\ProcessLogs;
use App\Models\Role;
use App\Models\User;
use App\Support\Organization;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuditLogTest extends TestCase
{
    use RefreshDatabase;

    public function test_changes_are_recorded_with_old_and_new_values_and_the_actor(): void
    {
        $actor = User::factory()->create(['role' => 1]);
        $this->actingAs($actor);

        $contact = Contact::create(['first_name' => 'Ada', 'last_name' => 'Lovelace', 'identity_number' => '10000000146']);
        $contact->update(['last_name' => 'Byron', 'identity_number' => '20000000046']);

        $log = ProcessLogs::where('subject_type', Contact::class)->where('subject_id', $contact->id)->where('process_type', 'change')->sole();
        $this->assertSame(['Lovelace', 'Byron'], $log->changes['last_name']);
        $this->assertSame(['•••', '••• (değişti)'], $log->changes['identity_number']);
        $this->assertSame($actor->id, $log->process_by);
        $this->assertSame('Kişi / kurum düzenlendi: Ada Byron', $log->process);

        $created = ProcessLogs::where('subject_type', Contact::class)->where('subject_id', $contact->id)->where('process_type', 'create')->sole();
        $this->assertSame([null, '•••'], $created->changes['identity_number']);
    }

    public function test_secrets_and_empty_changes_are_not_recorded(): void
    {
        $user = User::factory()->create();
        $before = ProcessLogs::count();

        $user->update(['password' => bcrypt('new-secret'), 'remember_token' => 'x']);
        $user->touch();

        $this->assertSame($before, ProcessLogs::count());
        $this->assertStringNotContainsString('new-secret', (string) ProcessLogs::pluck('changes')->toJson());
    }

    public function test_role_permissions_and_settings_are_recorded(): void
    {
        $role = Role::create(['key' => 'editor', 'name' => 'Editör']);
        $role->syncPermissions(['admin.access']);
        $role->syncPermissions(['admin.access']);
        app(Organization::class)->save(['short_name' => 'ÖD']);

        $this->assertSame(1, ProcessLogs::where('process', 'Rol yetkileri değişti: Editör')->count());
        $this->assertSame(['', 'admin.access'], ProcessLogs::where('process', 'Rol yetkileri değişti: Editör')->sole()->changes['permissions']);
        $this->assertTrue(ProcessLogs::where('process', 'Kurum ayarı eklendi: short_name')->exists());
    }

    public function test_owners_filter_the_log(): void
    {
        $owner = User::factory()->create(['role' => 1]);
        $this->actingAs($owner);
        $contact = Contact::create(['first_name' => 'Grace', 'last_name' => 'Hopper']);
        $contact->update(['last_name' => 'Murray']);

        $this->get('/admin/process-logs?subject='.urlencode(Contact::class).'&subject_id='.$contact->id)->assertOk()
            ->assertSee('Kişi / kurum düzenlendi: Grace Murray')
            ->assertSee('Hopper');
        $this->get('/admin/process-logs?type=delete')->assertOk()->assertDontSee('Grace Murray');
        $this->actingAs(User::factory()->create(['role' => 2]))->get('/admin/process-logs')->assertForbidden();
    }
}
