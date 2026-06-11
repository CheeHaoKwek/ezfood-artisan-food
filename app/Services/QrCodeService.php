<?php

namespace App\Services;

use App\Models\QrConfig;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

class QrCodeService
{
    /**
     * The mobile web entry URL the printed QR code resolves to (Step 2/3).
     */
    public function entryUrl(QrConfig $config): string
    {
        return route('app.entry', ['code' => $config->code]);
    }

    public function svg(QrConfig $config, int $size = 400): string
    {
        $renderer = new ImageRenderer(new RendererStyle($size), new SvgImageBackEnd());

        return (new Writer($renderer))->writeString($this->entryUrl($config));
    }
}
