<?php

namespace App\Support;

use HTMLPurifier;
use HTMLPurifier_Config;
use Illuminate\Support\Facades\File;

class HtmlSanitizer
{
    public function sanitize(?string $html): string
    {
        $config = $this->config();
        $config->set('HTML.Allowed', 'p,br,b,strong,i,em,u,ul,ol,li,h1,h2,h3,h4,h5,h6,blockquote,pre,code,a[href|title|target|rel],table,thead,tbody,tr,th,td');
        $config->set('HTML.Nofollow', true);

        return (new HTMLPurifier($config))->purify($html ?? '');
    }

    /**
     * Page content written by the organization's managers (e.g. the home
     * page): also images, alignment and simple layout blocks.
     */
    public function sanitizePage(?string $html): string
    {
        $config = $this->config();
        $config->set('HTML.Allowed', 'p[style],br,b,strong,i,em,u,s,span[style],div[style],hr,ul,ol,li,h1[style],h2[style],h3[style],h4[style],h5[style],h6[style],blockquote,pre,code,a[href|title|target|rel],img[src|alt|title|width|height|style],table[style],thead,tbody,tr,th[style],td[style]');
        $config->set('CSS.AllowedProperties', ['text-align', 'color', 'background-color', 'width', 'height', 'max-width', 'float', 'margin', 'margin-left', 'margin-right']);
        $config->set('URI.AllowedSchemes', ['http' => true, 'https' => true, 'mailto' => true, 'tel' => true]);

        return (new HTMLPurifier($config))->purify($html ?? '');
    }

    private function config(): HTMLPurifier_Config
    {
        $config = HTMLPurifier_Config::createDefault();
        $cachePath = storage_path('app/htmlpurifier');

        File::ensureDirectoryExists($cachePath);
        $config->set('Cache.SerializerPath', $cachePath);
        $config->set('Attr.AllowedFrameTargets', ['_blank']);

        return $config;
    }
}
