<?php

namespace App\Filament\Resources\SettingResource\Pages;

use App\Filament\Resources\SettingResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageSettings extends ManageRecords
{
    protected static string $resource = SettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Add Setting')
                ->mutateFormDataUsing(function (array $data): array {
                    if (empty($data['key'])) {
                        $data['key'] = 'stripe_checkout_url';
                    }
                    return $data;
                }),
        ];
    }
}

