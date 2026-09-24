<?php

namespace Tests\Concerns;

use Illuminate\Http\UploadedFile;

/**
 * Fake image uploads without the GD extension (UploadedFile::fake()->image()
 * needs it): a real, solid-colour PNG built by hand.
 */
trait MakesImages
{
    protected function fakePng(string $name, int $width, int $height): UploadedFile
    {
        $chunk = fn (string $type, string $data) => pack('N', strlen($data)).$type.$data.pack('N', crc32($type.$data));
        // Each row: filter byte 0, then RGB pixels.
        $rows = str_repeat("\0".str_repeat("\x80\x80\x80", $width), $height);

        $png = "\x89PNG\r\n\x1a\n"
            .$chunk('IHDR', pack('NNCCCCC', $width, $height, 8, 2, 0, 0, 0))
            .$chunk('IDAT', gzcompress($rows))
            .$chunk('IEND', '');

        return UploadedFile::fake()->createWithContent($name, $png);
    }
}
