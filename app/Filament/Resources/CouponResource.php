<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CouponResource\Pages;
use App\Models\Coupon;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Illuminate\Support\Str;

class CouponResource extends Resource
{
    protected static ?string $model = Coupon::class;

    public static function getNavigationGroup(): ?string
    {
        return 'Shop';
    }
    
    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-ticket';
    }
    
    protected static ?int $navigationSort = 40;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Coupon Information')
                    ->schema([
                        Forms\Components\TextInput::make('code')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(50)
                            ->uppercase()
                            ->helperText('Coupon code (will be converted to uppercase)'),
                        Forms\Components\Select::make('type')
                            ->options([
                                'flat' => 'Flat Amount',
                                'percentage' => 'Percentage',
                            ])
                            ->required()
                            ->default('percentage')
                            ->live(),
                        Forms\Components\TextInput::make('value')
                            ->label(fn ($get) => $get('type') === 'percentage' ? 'Percentage (%)' : 'Amount ($)')
                            ->required()
                            ->numeric()
                            ->prefix(fn ($get) => $get('type') === 'percentage' ? '' : '$')
                            ->suffix(fn ($get) => $get('type') === 'percentage' ? '%' : '')
                            ->helperText(fn ($get) => $get('type') === 'percentage' 
                                ? 'Percentage discount (e.g., 10 for 10%)'
                                : 'Fixed discount amount in dollars'),
                        Forms\Components\TextInput::make('min_order')
                            ->label('Minimum Order Amount')
                            ->numeric()
                            ->prefix('$')
                            ->default(0)
                            ->helperText('Minimum order amount required to use this coupon'),
                        Forms\Components\TextInput::make('max_discount')
                            ->label('Maximum Discount')
                            ->numeric()
                            ->prefix('$')
                            ->visible(fn ($get) => $get('type') === 'percentage')
                            ->helperText('Maximum discount amount (for percentage coupons)'),
                        Forms\Components\TextInput::make('usage_limit')
                            ->label('Total Usage Limit')
                            ->numeric()
                            ->helperText('Maximum number of times this coupon can be used (leave empty for unlimited)'),
                        Forms\Components\TextInput::make('usage_per_user')
                            ->label('Usage Per User')
                            ->numeric()
                            ->default(1)
                            ->helperText('How many times a single user can use this coupon'),
                        Forms\Components\DateTimePicker::make('expires_at')
                            ->label('Expires At')
                            ->helperText('Leave empty for no expiration'),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Active')
                            ->default(true),
                        Forms\Components\Textarea::make('description')
                            ->label('Description')
                            ->rows(3)
                            ->maxLength(500),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'flat' => 'info',
                        'percentage' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'flat' => 'Flat',
                        'percentage' => 'Percentage',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('value')
                    ->label('Discount')
                    ->formatStateUsing(function ($state, $record) {
                        if ($record->type === 'percentage') {
                            return $record->value . '%' . ($record->max_discount ? ' (max $' . number_format($record->max_discount, 2) . ')' : '');
                        }
                        return '$' . number_format($record->value, 2);
                    }),
                Tables\Columns\TextColumn::make('min_order')
                    ->label('Min Order')
                    ->money('usd')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('used_count')
                    ->label('Used')
                    ->formatStateUsing(function ($state, $record) {
                        return $record->usage_limit ? $record->used_count . '/' . $record->usage_limit : $record->used_count;
                    })
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('expires_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'flat' => 'Flat',
                        'percentage' => 'Percentage',
                    ]),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active')
                    ->placeholder('All')
                    ->trueLabel('Active only')
                    ->falseLabel('Inactive only'),
                Tables\Filters\Filter::make('expired')
                    ->label('Expired')
                    ->query(fn ($query) => $query->where('expires_at', '<', now()))
                    ->toggle(),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCoupons::route('/'),
            'create' => Pages\CreateCoupon::route('/create'),
            'edit' => Pages\EditCoupon::route('/{record}/edit'),
        ];
    }
}
