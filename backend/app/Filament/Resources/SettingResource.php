<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SettingResource\Pages;
use App\Models\Setting;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class SettingResource extends Resource
{
    protected static ?string $model = Setting::class;

    public static function getNavigationGroup(): ?string
    {
        return 'Settings';
    }

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-cog-8-tooth';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Stripe Checkout')
                ->description('Configure Stripe checkout URL used for Buy Now / Checkout redirect.')
                ->schema([
                    Forms\Components\TextInput::make('key')
                        ->label('Key')
                        ->default('stripe_checkout_url')
                        ->disabled()
                        ->dehydrated(true),
                    Forms\Components\TextInput::make('value')
                        ->label('Stripe Checkout URL')
                        ->placeholder('https://checkout.stripe.com/c/pay/...')
                        ->required()
                        ->url()
                        ->maxLength(500),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('key')->label('Key')->searchable(),
                Tables\Columns\TextColumn::make('value')->label('Value')->limit(50)->wrap(),
                Tables\Columns\TextColumn::make('updated_at')->dateTime()->label('Updated'),
            ])
            ->filters([])
            ->recordActions([
                EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageSettings::route('/'),
        ];
    }
}

