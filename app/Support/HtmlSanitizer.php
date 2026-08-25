<?php

namespace App\Support;

use HTMLPurifier;
use HTMLPurifier_Config;
use Illuminate\Support\Facades\File;

class HtmlSanitizer
{
    public function sanitize(?string $html): string
    {
        $config = HTMLPurifier_Config::createDefault();
        $cachePath = storage_path('app/htmlpurifier');

        File::ensureDirectoryExists($cachePath);
        $config->set('Cache.SerializerPath', $cachePath);
        $config->set('HTML.Allowed', 'p,br,b,strong,i,em,u,ul,ol,li,h1,h2,h3,h4,h5,h6,blockquote,pre,code,a[href|title|target|rel],table,thead,tbody,tr,th,td');
        $config->set('Attr.AllowedFrameTargets', ['_blank']);
        $config->set('HTML.Nofollow', true);

        return (new HTMLPurifier($config))->purify($html ?? '');
    }
}
