<?php

namespace App\Support;

use App\Models\Setting;
use Illuminate\Support\Facades\Storage;
use Throwable;

/**
 * The association running this installation: name, logo, colours, contact
 * details, notification addresses and home page, edited in the admin panel
 * (Ayarlar → Kurum ayarları). Code and views read these values here instead of
 * hardcoding one association's details.
 */
class Organization
{
    /** Directory on the private "local" disk for the logo, favicon and page images. */
    public const DIRECTORY = 'organization';

    /**
     * Social links shown in the footer and in emails: key => [label, icon].
     */
    public const SOCIALS = [
        'social_facebook' => ['Facebook', 'brand-facebook'],
        'social_x' => ['X', 'brand-x'],
        'social_instagram' => ['Instagram', 'brand-instagram'],
        'social_linkedin' => ['LinkedIn', 'brand-linkedin'],
        'social_youtube' => ['YouTube', 'brand-youtube'],
        'social_mastodon' => ['Mastodon', 'brand-mastodon'],
        'social_github' => ['GitHub', 'brand-github'],
        'social_whatsapp' => ['WhatsApp', 'brand-whatsapp'],
    ];

    private ?array $values = null;

    public function get(string $key, ?string $default = null): ?string
    {
        $this->values ??= $this->load();
        $value = $this->values[$key] ?? null;

        return $value === null || $value === '' ? $default : $value;
    }

    /**
     * Store values; null or empty removes the setting so the default applies.
     *
     * @param  array<string, string|null>  $values
     */
    public function save(array $values): void
    {
        foreach ($values as $key => $value) {
            $value === null || $value === ''
                ? Setting::whereKey($key)->delete()
                : Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        $this->values = null;
    }

    public function name(): string
    {
        return $this->get('name', (string) config('organization.name'));
    }

    public function shortName(): string
    {
        return $this->get('short_name', $this->name());
    }

    public function logoUrl(bool $absolute = false): ?string
    {
        return $this->fileUrl('logo_path', 'organization.logo', $absolute);
    }

    public function faviconUrl(): ?string
    {
        return $this->fileUrl('favicon_path', 'organization.favicon');
    }

    public function sourceUrl(): string
    {
        return $this->get('source_url', (string) config('organization.source_url'));
    }

    /**
     * Address that receives notices for the management (seminar requests,
     * email change requests...); null when none is set.
     */
    public function notificationEmail(): ?string
    {
        return $this->get('notification_email');
    }

    /**
     * Sender of announcements: [address, name]; falls back to the mail config.
     *
     * @return array{0: string, 1: string}
     */
    public function sender(): array
    {
        return [
            $this->get('sender_email', (string) config('mail.from.address')),
            $this->get('sender_name', $this->name()),
        ];
    }

    /**
     * Social links that are set: [['label', 'icon', 'url'], ...].
     */
    public function socialLinks(): array
    {
        $links = [];
        foreach (self::SOCIALS as $key => [$label, $icon]) {
            if ($url = $this->get($key)) {
                $links[] = ['label' => $label, 'icon' => $icon, 'url' => $url];
            }
        }

        return $links;
    }

    /**
     * Origins allowed to embed the iframe pages (login, register, seminars).
     *
     * @return string[]
     */
    public function frameAncestors(): array
    {
        return array_values(array_filter(array_map('trim', preg_split('/\s+/', (string) $this->get('frame_ancestors')))));
    }

    public function frameAncestorsPolicy(): string
    {
        return trim("frame-ancestors 'self' ".implode(' ', $this->frameAncestors()));
    }

    /**
     * CSS variables overriding the theme's primary colour, or null.
     */
    public function themeCss(): ?string
    {
        $hex = $this->get('primary_color');
        if (! $hex || ! preg_match('/^#[0-9a-f]{6}$/i', $hex)) {
            return null;
        }

        $rgb = sscanf($hex, '#%02x%02x%02x');
        $mix = fn (array $with, float $amount) => array_map(fn ($c, $w) => (int) round($c + ($w - $c) * $amount), $rgb, $with);
        $darken = $mix([0, 0, 0], .15);
        $light = $mix([255, 255, 255], .9);
        $hexOf = fn (array $c) => sprintf('#%02x%02x%02x', ...$c);

        return ':root,[data-bs-theme=light]{'
            ."--tblr-primary:{$hex};--tblr-primary-rgb:".implode(',', $rgb).';'
            .'--tblr-primary-darken:'.$hexOf($darken).';'
            .'--tblr-primary-lt:'.$hexOf($light).';--tblr-primary-lt-rgb:'.implode(',', $light).';'
            .'--tblr-link-color:'.$hexOf($darken).';--tblr-link-color-rgb:'.implode(',', $darken).';'
            .'--tblr-link-hover-color:'.$hexOf($mix([0, 0, 0], .35)).';}';
    }

    private function fileUrl(string $key, string $route, bool $absolute = false): ?string
    {
        $path = $this->get($key);
        if (! $path) {
            return null;
        }

        // The version busts browser caches when the file is replaced.
        $parameters = ['v' => substr(md5($path), 0, 8)];

        return $absolute
            ? rtrim((string) config('app.url'), '/').route($route, $parameters, false)
            : route($route, $parameters);
    }

    private function load(): array
    {
        try {
            return Setting::pluck('value', 'key')->all();
        } catch (Throwable) {
            // Before the settings table exists (fresh install, migrations).
            return [];
        }
    }

    public static function storedFile(?string $path): bool
    {
        return $path !== null && $path !== '' && Storage::disk('local')->exists($path);
    }
}
