<?php

namespace Tests\Feature;

use App\Models\CustomField;
use App\Models\User;
use App\Support\CustomFields;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileTabsTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_profile_is_split_into_tabs_with_fields_on_their_group_tab(): void
    {
        CustomField::create(['key' => 'nickname', 'label' => 'Takma ad', 'group' => 'personal', 'member_access' => 'editable']);
        CustomField::create(['key' => 'work_address', 'label' => 'İşyeri adresi', 'group' => 'work', 'member_access' => 'editable']);
        $user = User::factory()->create();

        $html = $this->actingAs($user)->get('/my-infos')->assertOk()->getContent();

        foreach (['tab-personal', 'tab-contact', 'tab-membership', 'tab-privacy'] as $tab) {
            $this->assertStringContainsString('id="'.$tab.'"', $html);
        }
        $this->assertMatchesRegularExpression('/id="tab-personal".*Takma ad.*id="tab-contact"/s', $html);
        $this->assertMatchesRegularExpression('/id="tab-contact".*İşyeri adresi.*id="tab-membership"/s', $html);
        $this->assertMatchesRegularExpression('/id="tab-privacy".*İletişim izinleri.*Kişisel verilerimin silinmesi/s', $html);
        $this->assertStringContainsString('Üyelik kaydınız yok.', $html);
    }

    public function test_saving_one_tab_leaves_the_other_tabs_fields_alone(): void
    {
        $nickname = CustomField::create(['key' => 'nickname', 'label' => 'Takma ad', 'group' => 'personal', 'member_access' => 'editable']);
        $work = CustomField::create(['key' => 'work_address', 'label' => 'İşyeri adresi', 'group' => 'work', 'member_access' => 'editable']);
        $user = User::factory()->create();

        $this->actingAs($user)->put('/my-fields', ['fields' => ['work_address' => 'Ankara'], 'anchor' => 'fields-contact'])->assertRedirect('/my-infos#fields-contact');
        $this->actingAs($user)->put('/my-fields', ['fields' => ['nickname' => 'penguen'], 'anchor' => 'fields-personal']);

        $values = app(CustomFields::class)->values($user->contact);
        $this->assertSame('Ankara', $values[$work->id]);
        $this->assertSame('penguen', $values[$nickname->id]);
    }
}
