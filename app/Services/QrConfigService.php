<?php

namespace App\Services;

use App\Models\QrConfig;

class QrConfigService
{
    /**
     * Resolve the QR config encoded in a scanned QR code.
     * The QR config is the source of truth for everything the subscriber can see and select.
     */
    public function resolveActiveByCode(string $code): QrConfig
    {
        return QrConfig::query()
            ->where('code', $code)
            ->where('is_active', true)
            ->whereHas('outlet', fn ($query) => $query->where('is_active', true))
            ->with(['outlet', 'mealSlots' => fn ($query) => $query->where('is_active', true)])
            ->firstOrFail();
    }
}
