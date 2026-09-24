<?php

namespace Modules\IdCard\Support;

use App\Models\Contact;
use App\Models\ContactAffiliation;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Modules\IdCard\Models\IdCard;
use Modules\IdCard\Models\IdCardTemplate;

/**
 * Issues a card for every active affiliation of a contact that has an active
 * card template. Cards are issued on demand, so a new affiliation gets its
 * card the first time the person opens their cards.
 */
class CardIssuer
{
    /**
     * The contact's cards for its active affiliations, issuing missing ones.
     *
     * @return Collection<int, IdCard>
     */
    public function cardsFor(Contact $contact): Collection
    {
        $templates = IdCardTemplate::where('is_active', true)->get()->keyBy('affiliation_type_id');

        if ($templates->isEmpty()) {
            return collect();
        }

        return $contact->affiliations()->active()
            ->whereIn('affiliation_type_id', $templates->keys())
            ->with('type')
            ->get()
            ->sortBy(fn (ContactAffiliation $affiliation) => $affiliation->type->sort)
            ->map(fn (ContactAffiliation $affiliation) => $this->issue($templates[$affiliation->affiliation_type_id], $affiliation))
            ->values();
    }

    public function issue(IdCardTemplate $template, ContactAffiliation $affiliation): IdCard
    {
        $card = IdCard::where('id_card_template_id', $template->id)->where('contact_affiliation_id', $affiliation->id)->first()
            ?? DB::transaction(function () use ($template, $affiliation) {
                // Lock the template row so concurrent issues do not take the same serial.
                IdCardTemplate::whereKey($template->id)->lockForUpdate()->first();
                $serial = (int) IdCard::where('id_card_template_id', $template->id)->max('serial') + 1;

                return IdCard::create([
                    'id_card_template_id' => $template->id,
                    'contact_id' => $affiliation->contact_id,
                    'contact_affiliation_id' => $affiliation->id,
                    'serial' => $serial,
                    'number' => $template->formatNumber($serial),
                    'verify_token' => IdCard::newVerifyToken(),
                ]);
            });

        return $card->setRelation('template', $template)->setRelation('affiliation', $affiliation);
    }
}
