<?php

namespace Tests\Feature;

use App\Models\Contact;
use App\Models\CustomField;
use App\Models\CustomFieldValue;
use App\Models\Role;
use App\Models\Tag;
use App\Models\User;
use App\Modules\ContactFields;
use App\Support\CustomFields;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TagsAndCustomFieldsTest extends TestCase
{
    use RefreshDatabase;

    private function fieldsManager(): User
    {
        $role = Role::create(['key' => 'fields-manager', 'name' => 'Alan sorumlusu']);
        $role->syncPermissions(['admin.access', 'fields.manage']);
        $user = User::factory()->create();
        $user->roles()->attach($role);

        return $user;
    }

    public function test_tags_are_defined_assigned_and_filtered(): void
    {
        $manager = $this->fieldsManager();
        $this->actingAs($manager)->post('/admin/tags', ['name' => 'Basın', 'color' => 'orange'])->assertRedirect();
        $tag = Tag::sole();

        $owner = User::factory()->create(['role' => 1]);
        $press = Contact::create(['first_name' => 'Ada', 'last_name' => 'Lovelace']);
        Contact::create(['first_name' => 'Grace', 'last_name' => 'Hopper']);

        $this->actingAs($owner)->put("/admin/contacts/{$press->id}/tags", ['tags' => [$tag->id]])->assertRedirect();
        $this->assertTrue($press->tags()->whereKey($tag->id)->exists());

        $this->actingAs($owner)->get('/admin/contacts?tag='.$tag->id)->assertOk()->assertSee('Ada Lovelace')->assertDontSee('Grace Hopper');
        $this->actingAs($manager)->get('/admin/tags')->assertOk()->assertSee('Basın');
        $this->actingAs(User::factory()->create(['role' => 2]))->post('/admin/tags', ['name' => 'X', 'color' => 'red'])->assertForbidden();
    }

    public function test_custom_fields_are_defined_and_validated(): void
    {
        $manager = $this->fieldsManager();

        $this->actingAs($manager)->post('/admin/custom-fields', [
            'key' => 'nickname', 'label' => 'Takma ad', 'group' => 'personal', 'type' => 'text',
            'member_access' => 'editable', 'applies_to' => 'person', 'sort' => 10, 'is_active' => '1',
        ])->assertRedirect('/admin/custom-fields');

        $this->actingAs($manager)->post('/admin/custom-fields', [
            'key' => 'shirt_size', 'label' => 'Tişört bedeni', 'group' => 'other', 'type' => 'select', 'options' => "S\nM\nL\n\nM",
            'member_access' => 'hidden', 'applies_to' => 'person', 'sort' => 20, 'is_active' => '1', 'is_required' => '1',
        ])->assertRedirect();

        $this->assertSame(['S', 'M', 'L'], CustomField::where('key', 'shirt_size')->value('options'));
        $this->actingAs($manager)->post('/admin/custom-fields', ['key' => 'Bad Key', 'label' => 'X', 'group' => 'nope', 'type' => 'text', 'member_access' => 'hidden', 'applies_to' => 'person', 'sort' => 1])
            ->assertSessionHasErrors(['key', 'group']);
        $this->actingAs($manager)->get('/admin/custom-fields')->assertOk()->assertSee('Takma ad')->assertSee('Tişört bedeni');
    }

    public function test_managers_fill_values_on_the_contact_page(): void
    {
        $size = CustomField::create(['key' => 'shirt_size', 'label' => 'Tişört bedeni', 'type' => 'select', 'options' => ['S', 'M'], 'is_required' => true]);
        $joined = CustomField::create(['key' => 'joined_on', 'label' => 'Katılma', 'type' => 'date']);
        $contact = Contact::create(['first_name' => 'Ada', 'last_name' => 'Lovelace']);
        $owner = User::factory()->create(['role' => 1]);

        $this->actingAs($owner)->put("/admin/contacts/{$contact->id}/fields", ['fields' => ['shirt_size' => 'XL']])->assertSessionHasErrorsIn('contactFields', 'fields.shirt_size');

        $this->actingAs($owner)->put("/admin/contacts/{$contact->id}/fields", ['fields' => ['shirt_size' => 'M', 'joined_on' => '2020-05-01']])->assertRedirect();
        $values = app(CustomFields::class)->values($contact);
        $this->assertSame('M', $values[$size->id]);
        $this->assertSame('2020-05-01', $values[$joined->id]);

        $this->actingAs($owner)->get("/admin/contacts/{$contact->id}")->assertOk()->assertSee('Ek bilgiler')->assertSee('Tişört bedeni');

        // Emptying a value removes it.
        $this->actingAs($owner)->put("/admin/contacts/{$contact->id}/fields", ['fields' => ['shirt_size' => 'S', 'joined_on' => '']]);
        $this->assertFalse(CustomFieldValue::where('custom_field_id', $joined->id)->exists());
    }

    public function test_people_see_and_edit_only_what_access_allows(): void
    {
        CustomField::create(['key' => 'nickname', 'label' => 'Takma ad', 'member_access' => 'editable']);
        $printed = CustomField::create(['key' => 'card_printed', 'label' => 'Kartı basıldı', 'type' => 'checkbox', 'member_access' => 'visible']);
        CustomField::create(['key' => 'internal_note', 'label' => 'Gizli not', 'member_access' => 'hidden']);
        $user = User::factory()->create();
        CustomFieldValue::create(['contact_id' => $user->contact_id, 'custom_field_id' => $printed->id, 'value' => '1']);

        $this->actingAs($user)->get('/my-infos')->assertOk()->assertSee('Takma ad')->assertSee('Kartı basıldı')->assertSee('Evet')->assertDontSee('Gizli not');

        $this->actingAs($user)->put('/my-fields', ['fields' => ['nickname' => 'penguen', 'card_printed' => '0', 'internal_note' => 'x']])->assertRedirect('/my-infos#fields');

        $values = app(CustomFields::class)->values($user->contact);
        $this->assertContains('penguen', $values);
        $this->assertSame('1', $values[$printed->id], 'A visible-only field is not changed by the person.');
        $this->assertCount(2, $values);
    }

    public function test_custom_fields_can_be_printed_on_id_cards(): void
    {
        $field = CustomField::create(['key' => 'nickname', 'label' => 'Takma ad']);
        $contact = Contact::create(['first_name' => 'Ada', 'last_name' => 'Lovelace']);
        CustomFieldValue::create(['contact_id' => $contact->id, 'custom_field_id' => $field->id, 'value' => 'penguen']);

        $fields = app(ContactFields::class);
        $this->assertSame('Takma ad', $fields->options()['custom.nickname']);
        $this->assertSame('penguen', $fields->value('custom.nickname', $contact));
    }
}
