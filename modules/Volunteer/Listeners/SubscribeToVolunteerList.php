<?php

namespace Modules\Volunteer\Listeners;

use App\Events\DashboardVisited;
use App\Services\MailgunMailingList;

class SubscribeToVolunteerList
{
    public function __construct(private MailgunMailingList $lists)
    {
    }

    public function handle(DashboardVisited $event): void
    {
        $list = config('volunteer.mailing_list');

        if (! $list || ! config('services.mailgun.secret')) {
            return;
        }

        rescue(fn () => $this->lists->upsertMember($list, $event->user->email, $event->user->name.' '.$event->user->surname), report: false);
    }
}
