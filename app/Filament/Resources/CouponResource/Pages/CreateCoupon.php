<?php

namespace App\Filament\Resources\CouponResource\Pages;

use App\Filament\Resources\CouponResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreateCoupon extends CreateRecord
{
    protected static string $resource = CouponResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Convert code to uppercase
        if (isset($data['code'])) {
            $data['code'] = strtoupper($data['code']);
        }
        
        return $data;
    }
}
