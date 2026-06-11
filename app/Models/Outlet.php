<?php

namespace App\Models;

use App\Enums\OutletType;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'type', 'address', 'contact_person', 'contact_phone', 'timezone', 'is_active'])]
class Outlet extends Model
{
    protected function casts(): array
    {
        return [
            'type' => OutletType::class,
            'is_active' => 'boolean',
        ];
    }

    public function qrConfigs(): HasMany
    {
        return $this->hasMany(QrConfig::class);
    }

    public function meals(): HasMany
    {
        return $this->hasMany(Meal::class);
    }

    public function consolidatedOrders(): HasMany
    {
        return $this->hasMany(ConsolidatedOrder::class);
    }
}
