<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomScriptResource\Pages;
use App\Models\CustomScript;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;

class CustomScriptResource extends Resource
{
    protected static ?string $model = CustomScript::class;

    protected static ?string $navigationLabel = 'Custom Scripts';
    
    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-code-bracket';
    }
    
    public static function getNavigationGroup(): ?string
    {
        return 'Settings';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Script Name')
                    ->required()
                    ->maxLength(255),

                Forms\Components\Select::make('position')
                    ->label('Position')
                    ->options([
                        'head' => 'Head (Before </head>)',
                        'body_start' => 'Body Start (After <body>)',
                        'body_end' => 'Body End (Before </body>)',
                    ])
                    ->required()
                    ->default('head')
                    ->helperText('Where should this script be inserted?'),

                Forms\Components\Textarea::make('code')
                    ->label('Script Code')
                    ->required()
                    ->rows(10)
                    ->helperText('Include the <script> tags or any HTML/CSS code')
                    ->columnSpanFull(),

                Forms\Components\Toggle::make('is_active')
                    ->label('Active')
                    ->default(true)
                    ->helperText('Only active scripts will be loaded on the website'),

                Forms\Components\TextInput::make('sort_order')
                    ->label('Sort Order')
                    ->numeric()
                    ->default(0)
                    ->helperText('Lower numbers load first'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('position')
                    ->colors([
                        'primary' => 'head',
                        'success' => 'body_start',
                        'warning' => 'body_end',
                    ])
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('sort_order')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('position')
                    ->options([
                        'head' => 'Head',
                        'body_start' => 'Body Start',
                        'body_end' => 'Body End',
                    ]),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active'),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCustomScripts::route('/'),
            'create' => Pages\CreateCustomScript::route('/create'),
            'edit' => Pages\EditCustomScript::route('/{record}/edit'),
        ];
    }
}
