<?php

namespace Tests\Feature;

use App\Models\ContactPhoto;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\Concerns\MakesImages;
use Tests\TestCase;

class ContactPhotoTest extends TestCase
{
    use MakesImages, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');
    }

    private function reviewer(): User
    {
        $role = Role::create(['key' => 'photo-reviewer', 'name' => 'Fotoğraf onaycısı']);
        $role->syncPermissions(['admin.access', 'photos.review']);
        $user = User::factory()->create();
        $user->roles()->attach($role);

        return $user;
    }

    private function upload(User $user): ContactPhoto
    {
        $this->actingAs($user)->post('/my-photo', ['photo' => $this->fakePng('me.png', 300, 400)])
            ->assertRedirect('/my-infos#photo');

        return $user->fresh()->contact->photos()->latest('id')->first();
    }

    public function test_an_uploaded_photo_waits_for_review_on_the_private_disk(): void
    {
        $user = User::factory()->create();

        $photo = $this->upload($user);

        $this->assertTrue($photo->isPending());
        Storage::disk('local')->assertExists($photo->path);
        $this->assertNull($user->fresh()->contact->approvedPhoto);
    }

    public function test_small_or_non_image_files_are_refused(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post('/my-photo', ['photo' => $this->fakePng('tiny.png', 100, 100)])->assertSessionHasErrors('photo');
        $this->actingAs($user)->post('/my-photo', ['photo' => UploadedFile::fake()->create('cv.pdf', 10, 'application/pdf')])->assertSessionHasErrors('photo');

        $this->assertSame(0, ContactPhoto::count());
    }

    public function test_approving_keeps_one_photo_and_rejecting_removes_the_file(): void
    {
        $user = User::factory()->create();
        $reviewer = $this->reviewer();

        $first = $this->upload($user);
        $this->actingAs($reviewer)->patch("/admin/photos/{$first->id}/approve")->assertRedirect();
        $this->assertTrue($user->fresh()->contact->approvedPhoto->is($first));

        $second = $this->upload($user);
        $this->actingAs($reviewer)->patch("/admin/photos/{$second->id}/reject", ['reason' => 'Yüz görünmüyor'])->assertRedirect();
        Storage::disk('local')->assertMissing($second->path);
        $this->assertSame('Yüz görünmüyor', $second->fresh()->rejection_reason);
        $this->assertTrue($user->fresh()->contact->approvedPhoto->is($first), 'The approved photo stays after a rejection.');

        $third = $this->upload($user);
        $this->actingAs($reviewer)->patch("/admin/photos/{$third->id}/approve");
        Storage::disk('local')->assertMissing($first->path);
        $this->assertSame([$third->id], $user->fresh()->contact->photos()->pluck('id')->all());
    }

    public function test_photos_are_served_only_to_the_owner_and_reviewers(): void
    {
        $user = User::factory()->create();
        $photo = $this->upload($user);

        $this->actingAs($user)->get("/photos/{$photo->id}")->assertOk();
        $this->actingAs($this->reviewer())->get("/photos/{$photo->id}")->assertOk();
        $this->actingAs(User::factory()->create())->get("/photos/{$photo->id}")->assertForbidden();
        auth()->logout();
        $this->get("/photos/{$photo->id}")->assertRedirect('/login');
    }

    public function test_the_review_queue_needs_the_permission(): void
    {
        $this->upload(User::factory()->create());

        $this->actingAs($this->reviewer())->get('/admin/photos')->assertOk()->assertSee('Onayla');
        $this->actingAs(User::factory()->create(['role' => 2]))->get('/admin/photos')->assertForbidden();
    }

    public function test_the_photo_is_managed_on_the_profile_page(): void
    {
        $user = User::factory()->create();
        $this->upload($user);

        $this->actingAs($user)->get('/my-infos')->assertOk()->assertSee('id="photo"', false)->assertSee('Onay bekliyor')->assertSee('Onaya gönder');
        $this->actingAs($user)->get('/my-photo')->assertRedirect('/my-infos#photo')->assertStatus(301);
    }

    public function test_managers_see_the_photo_on_a_profile_without_changing_it(): void
    {
        $user = User::factory()->create();
        $photo = $this->upload($user);
        $photo->approve(User::factory()->create(['role' => 1]));

        $this->actingAs(User::factory()->create(['role' => 1]))->get("/admin/users/{$user->id}")->assertOk()
            ->assertSee(route('photos.show', $photo), false)
            ->assertDontSee('Onaya gönder');
    }

    public function test_the_owner_can_delete_their_photos(): void
    {
        $user = User::factory()->create();
        $photo = $this->upload($user);

        $this->actingAs($user)->delete('/my-photo')->assertRedirect('/my-infos#photo');

        $this->assertSame(0, ContactPhoto::count());
        Storage::disk('local')->assertMissing($photo->path);
    }
}
