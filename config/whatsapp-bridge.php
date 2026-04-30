<?php

return [

    /*
    |--------------------------------------------------------------------------
    | WhatsApp Bridge URL
    |--------------------------------------------------------------------------
    |
    | The base URL of the running Baileys WhatsApp bridge service.
    | Example: http://localhost:3000  or  http://whatsapp-bridge:3000
    |
    */
    'bridge_url' => env('WHATSAPP_BRIDGE_URL', 'http://localhost:3000'),

    /*
    |--------------------------------------------------------------------------
    | API Key
    |--------------------------------------------------------------------------
    |
    | Optional Bearer token to secure the bridge REST API.
    | Must match WA_API_KEY (or similar) set on the bridge side.
    | Leave null to disable authorization header.
    |
    */
    'api_key' => env('WHATSAPP_BRIDGE_API_KEY'),

    /*
    |--------------------------------------------------------------------------
    | HTTP Timeout
    |--------------------------------------------------------------------------
    |
    | Maximum seconds to wait for a response from the bridge.
    |
    */
    'timeout' => env('WHATSAPP_BRIDGE_TIMEOUT', 10),

];
