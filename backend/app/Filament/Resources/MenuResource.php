<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MenuResource\Pages;
use App\Models\Menu;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Forms;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;

class MenuResource extends Resource
{
    protected static ?string $model = Menu::class;
    
    public static function getNavigationGroup(): ?string
    {
        return 'Settings';
    }
    
    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-bars-3';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\Section::make('Menu Item Information')
                    ->schema([
                        Forms\Components\TextInput::make('label')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('e.g., Home, Products, About'),
                        Forms\Components\TextInput::make('url')
                            ->label('URL')
                            ->url()
                            ->maxLength(255)
                            ->placeholder('e.g., / or https://example.com')
                            ->helperText('Leave empty if using route'),
                        Forms\Components\TextInput::make('route')
                            ->maxLength(255)
                            ->placeholder('e.g., products.index')
                            ->helperText('Laravel route name. Leave empty if using URL'),
                        Forms\Components\Select::make('target')
                            ->options([
                                '_self' => 'Same Window',
                                '_blank' => 'New Window',
                            ])
                            ->default('_self'),
                        Forms\Components\TextInput::make('icon')
                            ->maxLength(255)
                            ->placeholder('heroicon-o-home')
                            ->helperText('Heroicon name (optional)'),
                        Forms\Components\TextInput::make('css_class')
                            ->label('CSS Class')
                            ->maxLength(255)
                            ->placeholder('custom-class'),
                        Forms\Components\TextInput::make('sort_order')
                            ->label('Sort Order')
                            ->numeric()
                            ->default(0)
                            ->helperText('Lower numbers appear first'),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Active')
                            ->default(true),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('label')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('url')
                    ->label('URL')
                    ->searchable()
                    ->limit(30),
                Tables\Columns\TextColumn::make('route')
                    ->searchable()
                    ->limit(20),
                Tables\Columns\TextColumn::make('target')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        '_self' => 'gray',
                        '_blank' => 'info',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('icon')
                    ->searchable(),
                Tables\Columns\TextColumn::make('sort_order')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->label('Active'),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')->label('Active'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
            ])
            ->defaultSort('sort_order');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMenus::route('/'),
            'create' => Pages\CreateMenu::route('/create'),
            'edit' => Pages\EditMenu::route('/{record}/edit'),
        ];
    }
}
