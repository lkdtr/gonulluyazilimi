<?php

namespace Tests\Feature;

use App\Models\LegalRepresentation;
use App\Models\LegalRepresentationCandidate;
use App\Models\LegalRepresentationVolunteer;
use App\Models\ProcessLogs;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class LegalRepresentationTest extends TestCase
{
    use RefreshDatabase;

    public function test_member_can_consent_to_contact_sharing_with_a_representative(): void
    {
        Mail::fake();
        $user = User::factory()->create();
        $representation = LegalRepresentation::where('city', 'Antalya')->firstOrFail();

        $this->actingAs($user)->post(route('representations.consent.store', $representation), [
            'contact_consent' => '1',
        ])->assertRedirect(route('representations.index'));

        $this->assertDatabaseHas('legal_representation_volunteers', ['legal_representation_id' => $representation->id, 'user_id' => $user->id, 'contact_consent' => 1]);
        $this->assertDatabaseHas('process_logs', ['process_by' => $user->id, 'process_type' => 'create']);
    }

    public function test_member_can_submit_a_legal_representative_candidacy(): void
    {
        Mail::fake();
        $user = User::factory()->create();
        $representation = LegalRepresentation::where('city', 'Ordu')->firstOrFail();

        $this->actingAs($user)->post(route('representations.candidate.store'), [
            'legal_representation_id' => $representation->id,
            'is_lkd_member' => '1',
            'membership_number' => '123',
            'local_area' => 'Altınordu',
            'motivation' => 'Yerel topluluğu geliştirmek istiyorum.',
            'contact_consent' => '1',
        ])->assertSessionHas('success-status');

        $this->assertDatabaseHas('legal_representation_candidates', ['legal_representation_id' => $representation->id, 'user_id' => $user->id, 'status' => 'pending']);
    }
}
