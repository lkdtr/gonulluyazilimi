<?php

return [
    // netgsm, or log to write messages to the log instead of sending them.
    'sms' => env('MESSAGING_SMS_DRIVER', 'netgsm'),

    // bridge (needs WHATSAPP_BRIDGE_URL), or log.
    'whatsapp' => env('MESSAGING_WHATSAPP_DRIVER', 'bridge'),
];
