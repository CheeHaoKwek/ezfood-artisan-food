<?php

namespace App\Services;

use App\Models\QrConfig;
use App\Models\User;

class RegistrationService
{
    /**
     * Create a subscriber account registered through a tenant QR link.
     *
     * Tenant association always comes from the QR config, never from request
     * input — callers must pass only validated profile data.
     */
    public function register(QrConfig $qrConfig, array $data): User
    {
        return User::create([
            ...$data,
            'outlet_id' => $qrConfig->outlet_id,
            'registered_qr_config_id' => $qrConfig->id,
        ]);
    }
}
