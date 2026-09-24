<?php

use Modules\Admin\AdminServiceProvider;
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
    | when enabled. A module is enabled only when its MODULE_* variable is set
    | to true in .env; a missing variable leaves it off, so a new installation
    | starts with the core and turns on what it needs. A module listed in
    | "requires" of an enabled module is enabled automatically.
    |
    | Migrations of every module are always loaded, so the schema does not
    | depend on which modules are enabled. A "locked" module cannot be
    | disabled.
    |
    */

    'modules' => [

        'admin' => [
            'locked' => true,
            'provider' => AdminServiceProvider::class,
        ],

        'volunteer' => [
            'enabled' => env('MODULE_VOLUNTEER', false),
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
            'enabled' => env('MODULE_EMAIL_CHANGE', false),
            'provider' => EmailChangeServiceProvider::class,
        ],

        'announcements' => [
            'enabled' => env('MODULE_ANNOUNCEMENTS', false),
            'provider' => AnnouncementsServiceProvider::class,
        ],

        'seminar' => [
            'enabled' => env('MODULE_SEMINAR', false),
            'provider' => SeminarServiceProvider::class,
        ],

        'lkd-young' => [
            'enabled' => env('MODULE_LKD_YOUNG', false),
            'provider' => LkdYoungServiceProvider::class,
            'requires' => ['mail-forwarding'],
        ],

        'representation' => [
            'enabled' => env('MODULE_REPRESENTATION', false),
            'provider' => RepresentationServiceProvider::class,
        ],

    ],

];
