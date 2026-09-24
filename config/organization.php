<?php

/*
|--------------------------------------------------------------------------
| Organization defaults
|--------------------------------------------------------------------------
|
| Used until the value is set in the admin panel (Ayarlar → Kurum ayarları),
| which stores it in the "settings" table. Kept neutral so a new
| installation for another association shows no LKD details.
|
*/

return [
    'name' => env('APP_NAME', 'Dernek'),
    // Source of this free software, linked from the footer.
    'source_url' => env('ORGANIZATION_SOURCE_URL', 'https://github.com/lkdtr/dernekyazilimi'),
];
