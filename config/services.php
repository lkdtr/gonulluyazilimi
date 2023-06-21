<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'netgsm' => [
        'client'     => 'xml', //http or xml
        'http'       => [
            'endpoint' => 'https://api.netgsm.com.tr/sms/send/get/',
        ],
        'xml'        => [
            'endpoint' => 'api.netgsm.com.tr/sms/send/xml',
        ],
        'username'   => env('NETGSM_USERNAME'),
        'password'   => env('NETGSM_PASSWORD'),
        'originator' => env('NETGSM_ORGINATOR'), // Sender name.
        'timeout'    => 60,
    ],

    'postfixadmin' => [
        'server' => env('POSTFIXADMIN_SERVER'),
        'username' => env('POSTFIXADMIN_USERNAME'),
        'password' => env('POSTFIXADMIN_PASSWORD'),
    ],

];
