<?php

use Modules\Announcements\AnnouncementsServiceProvider;
use Modules\EmailChange\EmailChangeServiceProvider;
use Modules\LkdYoung\LkdYoungServiceProvider;
use Modules\MailForwarding\MailForwardingServiceProvider;
use Modules\Reference\ReferenceServiceProvider;
use Modules\Representation\RepresentationServiceProvider;
use Modules\Seminar\SeminarServiceProvider;
use Modules\Volunteer\VolunteerServiceProvider;

return [

    /*
    |--------------------------------------------------------------------------
    | Modules
    |--------------------------------------------------------------------------
    |
    | Every feature outside the core lives in modules/<Name> and is booted only
    | when enabled. A module listed in "requires" of an enabled module is
    | enabled automatically; so a module that only the volunteer module needs
    | (enabled => false) is switched off together with it.
    |
    | Migrations of every module are always loaded, so the schema does not
    | depend on which modules are enabled.
    |
    */

    'modules' => [

        'volunteer' => [
            'enabled' => env('MODULE_VOLUNTEER', true),
            'provider' => VolunteerServiceProvider::class,
            'requires' => ['mail-forwarding', 'reference'],
        ],

        'mail-forwarding' => [
            'enabled' => env('MODULE_MAIL_FORWARDING', false),
            'provider' => MailForwardingServiceProvider::class,
        ],

        'reference' => [
            'enabled' => env('MODULE_REFERENCE', false),
            'provider' => ReferenceServiceProvider::class,
        ],

        'email-change' => [
            'enabled' => env('MODULE_EMAIL_CHANGE', true),
            'provider' => EmailChangeServiceProvider::class,
        ],

        'announcements' => [
            'enabled' => env('MODULE_ANNOUNCEMENTS', true),
            'provider' => AnnouncementsServiceProvider::class,
        ],

        'seminar' => [
            'enabled' => env('MODULE_SEMINAR', true),
            'provider' => SeminarServiceProvider::class,
        ],

        'lkd-young' => [
            'enabled' => env('MODULE_LKD_YOUNG', true),
            'provider' => LkdYoungServiceProvider::class,
            'requires' => ['mail-forwarding'],
        ],

        'representation' => [
            'enabled' => env('MODULE_REPRESENTATION', true),
            'provider' => RepresentationServiceProvider::class,
        ],

    ],

];
