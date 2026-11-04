<?php

namespace App\Services;

use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

class QrCode
{
    /**
     * QR Code berformat SVG sebagai data URI, siap dipakai di <img src="...">.
     */
    public static function dataUri(string $text, int $size = 200): string
    {
        $renderer = new ImageRenderer(new RendererStyle($size, 1), new SvgImageBackEnd());
        $svg = (new Writer($renderer))->writeString($text);

        return 'data:image/svg+xml;base64,' . base64_encode($svg);
    }
}