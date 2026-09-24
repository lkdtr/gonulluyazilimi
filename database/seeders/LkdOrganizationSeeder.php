<?php

namespace Database\Seeders;

use App\Support\Organization;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

/**
 * LKD's organization settings, as they were hardcoded before the settings
 * screen existed. Run once on the LKD installation:
 *
 *   php artisan db:seed --class=LkdOrganizationSeeder --force
 *
 * Only settings that are still empty are filled, so values edited in the
 * admin panel are kept.
 */
class LkdOrganizationSeeder extends Seeder
{
    public function run(Organization $organization): void
    {
        $values = [
            'name' => 'Linux Kullanıcıları Derneği',
            'short_name' => 'LKD',
            'website_url' => 'https://www.lkd.org.tr',
            'contact_email' => 'gonullu@lkd.org.tr',
            'phone' => '+90 850 307 4502',
            'address' => 'PK 50, 06430 Yenişehir / Ankara',
            'social_facebook' => 'https://www.facebook.com/lkdtr',
            'social_x' => 'https://twitter.com/lkdtr',
            'social_instagram' => 'https://www.instagram.com/lkdorgtr/',
            'social_linkedin' => 'https://www.linkedin.com/company/linux-kullanicilari-dernegi/',
            'social_youtube' => 'https://youtube.com/channel/UC8tIk0G-bmwVoWQI0qHaQmQ',
            'social_whatsapp' => 'https://api.whatsapp.com/send?phone=908503074502',
            'notification_email' => 'yk@lkd.org.tr',
            'sender_email' => 'gonullu@lkd.org.tr',
            'sender_name' => 'Linux Kullanıcıları Derneği',
            'frame_ancestors' => "https://lkd.org.tr\nhttps://www.lkd.org.tr",
            'ga_measurement_id' => 'G-FH9QSFK0HF',
            'gtm_container_id' => 'GTM-T4XWJ3LM',
        ];

        if (! $organization->get('logo_path') && is_file($logo = public_path('images/lkd-gonullusu.png'))) {
            $path = Organization::DIRECTORY.'/lkd-gonullusu.png';
            Storage::disk('local')->put($path, file_get_contents($logo));
            $values['logo_path'] = $path;
        }

        $organization->save(array_filter($values, fn ($value, $key) => $organization->get($key) === null, ARRAY_FILTER_USE_BOTH));
    }
}
