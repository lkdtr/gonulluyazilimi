<?php

namespace Modules\IdCard\Tests\Feature;

use App\Models\Contact;
use App\Models\ContactPhoto;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Modules\IdCard\Models\IdCard;
use Modules\IdCard\Models\IdCardTemplate;
use Modules\IdCard\Support\CardView;
use Tests\Concerns\MakesImages;
use Tests\TestCase;

class IdCardTest extends TestCase
{
    use MakesImages, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');
    }

    private function volunteer(array $attributes = []): User
    {
        $user = User::factory()->create($attributes + ['name' => 'Ahmet', 'surname' => 'Yılmaz']);
        $user->contact->affiliate('volunteer');

        return $user;
    }

    private function approvePhoto(User $user): void
    {
        $photo = $user->contact->photos()->create(['path' => $this->fakePng('me.png', 300, 400)->store(ContactPhoto::DIRECTORY, 'local')]);
        $photo->approve(User::factory()->create(['role' => 1]));
    }

    private function cardManager(): User
    {
        $role = Role::create(['key' => 'card-manager', 'name' => 'Kart sorumlusu']);
        $role->syncPermissions(['admin.access', 'id-cards.manage']);
        $user = User::factory()->create();
        $user->roles()->attach($role);

        return $user;
    }

    public function test_volunteer_and_member_templates_are_created(): void
    {
        $this->assertEqualsCanonicalizing(
            ['volunteer', 'member'],
            IdCardTemplate::with('affiliationType')->get()->pluck('affiliationType.key')->all()
        );
    }

    public function test_a_card_is_issued_per_active_affiliation_with_a_template(): void
    {
        $user = $this->volunteer(['lkd_user_id' => 1234]);
        $user->contact->affiliate('board');
        $this->approvePhoto($user);

        $this->actingAs($user)->get('/my-cards')->assertOk()
            ->assertSee('Gönüllü Kartı')
            ->assertSee('Üye Kartı')
            ->assertSee('G-000001')
            ->assertSee('U-000001')
            ->assertSee('1234')
            ->assertSee('<svg', false);

        $this->assertSame(2, IdCard::count(), 'The board affiliation has no template, so no card.');

        // Opening the page again issues nothing new.
        $this->actingAs($user)->get('/my-cards');
        $this->assertSame(2, IdCard::count());
    }

    public function test_serials_follow_each_other_within_a_template(): void
    {
        foreach (['Ada', 'Grace'] as $name) {
            $user = $this->volunteer(['name' => $name]);
            $this->approvePhoto($user);
            $this->actingAs($user)->get('/my-cards');
        }

        $this->assertSame(['G-000001', 'G-000002'], IdCard::orderBy('id')->pluck('number')->all());
    }

    public function test_the_card_waits_for_an_approved_photo(): void
    {
        $user = $this->volunteer();

        $this->actingAs($user)->get('/my-cards')->assertOk()
            ->assertSee('Fotoğrafınız onaylanınca kartınız burada görünecek.')
            ->assertDontSee('Yazdır');
    }

    public function test_a_template_can_do_without_photo(): void
    {
        IdCardTemplate::query()->update(['requires_photo' => false]);
        $user = $this->volunteer();

        $this->actingAs($user)->get('/my-cards')->assertOk()->assertSee('Yazdır');
    }

    public function test_the_verification_page_shows_a_masked_name_and_follows_the_affiliation(): void
    {
        $user = $this->volunteer();
        $this->approvePhoto($user);
        $this->actingAs($user)->get('/my-cards');
        $card = IdCard::first();
        auth()->logout();

        $this->get("/kart/{$card->verify_token}")->assertOk()
            ->assertSee('Geçerli kart')
            ->assertSee('Ahmet Y****')
            ->assertDontSee('Yılmaz')
            ->assertSee($card->number)
            ->assertHeader('X-Robots-Tag', 'noindex, nofollow');

        $user->contact->endAffiliation('volunteer');
        $this->travel(1)->days();

        $this->get("/kart/{$card->verify_token}")->assertOk()->assertSee('Geçerliliği sona ermiş kart');
    }

    public function test_a_renewed_qr_code_voids_the_old_one(): void
    {
        $user = $this->volunteer();
        $this->approvePhoto($user);
        $this->actingAs($user)->get('/my-cards');
        $card = IdCard::first();
        $oldToken = $card->verify_token;

        $this->actingAs($user)->post("/my-cards/{$card->id}/renew")->assertRedirect('/my-cards');
        $this->actingAs(User::factory()->create())->post("/my-cards/{$card->id}/renew")->assertForbidden();

        $this->get("/kart/{$oldToken}")->assertNotFound()->assertSee('Kart bulunamadı');
        $this->get('/kart/'.$card->fresh()->verify_token)->assertOk()->assertSee('Geçerli kart');
    }

    public function test_managers_revoke_and_restore_cards(): void
    {
        $user = $this->volunteer();
        $this->approvePhoto($user);
        $this->actingAs($user)->get('/my-cards');
        $card = IdCard::first();
        $manager = $this->cardManager();

        $this->actingAs($manager)->get('/admin/id-cards/issued')->assertOk()->assertSee($card->number);
        $this->actingAs($manager)->patch("/admin/id-cards/issued/{$card->id}/revoke", ['reason' => 'Kayıp'])->assertRedirect();
        $this->get("/kart/{$card->verify_token}")->assertSee('İptal edilmiş kart');

        $this->actingAs($manager)->patch("/admin/id-cards/issued/{$card->id}/restore")->assertRedirect();
        $this->get("/kart/{$card->verify_token}")->assertSee('Geçerli kart');
    }

    public function test_templates_are_edited_by_card_managers_only(): void
    {
        $template = IdCardTemplate::whereHas('affiliationType', fn ($query) => $query->where('key', 'volunteer'))->first();

        $this->actingAs(User::factory()->create(['role' => 2]))->get('/admin/id-cards')->assertForbidden();

        $manager = $this->cardManager();
        $this->actingAs($manager)->get('/admin/id-cards')->assertOk()->assertSee('Gönüllü Kartı');
        $this->actingAs($manager)->put("/admin/id-cards/templates/{$template->id}", [
            'name' => 'Gönüllü Kimlik Kartı',
            'organization_name' => 'Örnek Derneği',
            'background_color' => '#FFFFFF',
            'text_color' => '#111111',
            'accent_color' => '#ff0000',
            'number_prefix' => 'GV-',
            'number_digits' => 4,
            'fields' => ['email', 'city'],
            'is_active' => '1',
            'requires_photo' => '1',
            'logo' => $this->fakePng('logo.png', 200, 80),
        ])->assertRedirect('/admin/id-cards');

        $template->refresh();
        $this->assertSame('Gönüllü Kimlik Kartı', $template->name);
        $this->assertSame('#ffffff', $template->background_color);
        $this->assertSame(['email', 'city'], $template->fields);
        $this->assertSame('GV-0001', $template->formatNumber(1));
        Storage::disk('local')->assertExists($template->logo_path);
        $this->get("/id-card-logos/{$template->id}")->assertOk();

        $this->actingAs($manager)->put("/admin/id-cards/templates/{$template->id}", [
            'name' => 'X', 'organization_name' => 'X', 'background_color' => 'red', 'text_color' => '#111111',
            'accent_color' => '#ff0000', 'number_digits' => 4, 'fields' => ['national_id'],
        ])->assertSessionHasErrors(['background_color', 'fields.0']);
    }

    public function test_a_switched_off_template_hides_cards_and_fails_verification(): void
    {
        $user = $this->volunteer();
        $this->approvePhoto($user);
        $this->actingAs($user)->get('/my-cards');
        $card = IdCard::first();

        $card->template->update(['is_active' => false]);

        $this->actingAs($user)->get('/my-cards')->assertSee('Henüz kartınız yok');
        $this->get("/kart/{$card->verify_token}")->assertSee('Geçersiz kart');
    }

    public function test_the_qr_code_points_to_the_site_domain_whatever_domain_is_visited(): void
    {
        config(['app.url' => 'https://portal.example.org']);
        IdCardTemplate::query()->update(['requires_photo' => false]);
        $user = $this->volunteer();

        $html = $this->actingAs($user)->get('http://old.example.org/my-cards')->assertOk()->getContent();
        $card = IdCard::first();

        $this->assertStringContainsString('href="https://portal.example.org/kart/'.$card->verify_token.'"', $html);
        $this->assertStringNotContainsString('old.example.org/kart', $html);
        $this->assertSame('https://portal.example.org/kart/'.$card->verify_token, CardView::verifyUrl($card));
    }

    public function test_masked_names_keep_first_names_and_the_surname_initial(): void
    {
        $this->assertSame('Ayşe Nur Ş****', CardView::maskedName(new Contact(['first_name' => 'Ayşe Nur', 'last_name' => 'şahin'])));
        $this->assertSame('Ada', CardView::maskedName(new Contact(['first_name' => 'Ada', 'last_name' => ''])));
    }
}
