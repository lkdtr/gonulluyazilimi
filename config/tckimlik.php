<?php

return [
    'base_url' => env('TCKIMLIK_BASE_URL', 'https://tckimlik.linux.org.tr'),

    'soap_namespace' => env('TCKIMLIK_SOAP_NAMESPACE', 'http://tckimlik.linux.org.tr/WS'),

    'tor_enabled' => env('TCKIMLIK_TOR_ENABLED', false),

    'tor_proxy' => env('TCKIMLIK_TOR_PROXY', 'socks5h://127.0.0.1:9050'),
];
