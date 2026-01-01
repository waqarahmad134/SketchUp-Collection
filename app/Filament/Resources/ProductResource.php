<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
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

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Basic Information')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (string $context, $state, callable $set) => $context === 'create' ? $set('slug', Str::slug($state)) : null),
                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        Forms\Components\Textarea::make('description')
                            ->required()
                            ->rows(3)
                            ->maxLength(500),
                        Forms\Components\RichEditor::make('full_description')
                            ->columnSpanFull(),
                    ])->columns(2),

                Section::make('Pricing')
                    ->schema([
                        Forms\Components\TextInput::make('price')
                            ->required()
                            ->numeric()
                            ->prefix('$')
                            ->default(0),
                        Forms\Components\TextInput::make('original_price')
                            ->required()
                            ->numeric()
                            ->prefix('$')
                            ->default(0),
                    ])->columns(2),

                Section::make('Product Details')
                    ->schema([
                        Forms\Components\Select::make('category_id')
                            ->label('Category')
                            ->relationship('category', 'name', fn ($query) => $query->where('is_active', true))
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')->required(),
                                Forms\Components\TextInput::make('slug')->required(),
                            ]),
                        Forms\Components\Toggle::make('is_bundle')
                            ->label('Is Bundle')
                            ->default(false),
                        Forms\Components\Toggle::make('is_digital')
                            ->label('Is Digital')
                            ->default(true),
                        Forms\Components\TextInput::make('file_count')
                            ->numeric()
                            ->default(0),
                        Forms\Components\TextInput::make('file_size')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('sort_order')
                            ->numeric()
                            ->default(0),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Active')
                            ->default(true),
                    ])->columns(3),

                Section::make('Media')
                    ->schema([
                        Forms\Components\FileUpload::make('image')
                            ->image()
                            ->required()
                            ->directory('products')
                            ->disk('public')
                            ->visibility('public'),
                        Forms\Components\FileUpload::make('images')
                            ->image()
                            ->multiple()
                            ->directory('products/gallery')
                            ->disk('public')
                            ->visibility('public'),
                        Forms\Components\FileUpload::make('download_file')
                            ->label('Digital File')
                            ->directory('products/downloads')
                            ->disk('private')
                            ->visibility('private')
                            ->helperText('Private download file for digital products'),
                        Forms\Components\Repeater::make('download_links')
                            ->label('Download Links')
                            ->schema([
                                Forms\Components\TextInput::make('title')
                                    ->label('Title')
                                    ->placeholder('e.g., Mega Drive link')
                                    ->required(),
                                Forms\Components\TextInput::make('url')
                                    ->label('URL')
                                    ->url()
                                    ->required(),
                            ])
                            ->addActionLabel('Add Link')
                            ->default([])
                            ->columns(2)
                            ->hidden(fn ($get) => !$get('is_digital')),
                    ]),

                Section::make('Additional Information')
                    ->schema([
                        Forms\Components\TagsInput::make('features')
                            ->placeholder('Add a feature and press Enter'),
                        Forms\Components\Select::make('tags')
                            ->label('Tags')
                            ->relationship('tags', 'name', fn ($query) => $query->where('type', 'product')->where('is_active', true))
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')->required(),
                                Forms\Components\TextInput::make('slug')->required(),
                                Forms\Components\Select::make('type')->options(['product' => 'Product'])->default('product'),
                            ]),
                        Forms\Components\Select::make('included_products')
                            ->multiple()
                            ->options(Product::pluck('title', 'id'))
                            ->visible(fn ($get) => $get('is_bundle')),
                    ]),

                Section::make('SEO & Meta Tags')
                    ->schema([
                        Section::make('Basic SEO')
                            ->schema([
                                        Forms\Components\TextInput::make('meta_title')
                                            ->label('Meta Title')
                                            ->maxLength(60)
                                            ->helperText('Leave blank to use product title')
                                            ->columnSpanFull(),
                                        Forms\Components\Textarea::make('meta_description')
                                            ->label('Meta Description')
                                            ->maxLength(155)
                                            ->rows(3)
                                            ->helperText('Leave blank to use product description')
                                            ->columnSpanFull(),
                                        Forms\Components\TextInput::make('canonical_url')
                                            ->label('Canonical URL')
                                            ->url()
                                            ->helperText('Leave blank to use current URL')
                                            ->columnSpanFull(),
                                        Forms\Components\Select::make('robots_index')
                                            ->label('Robots Index')
                                            ->options([
                                                'index' => 'Index',
                                                'noindex' => 'No Index',
                                            ])
                                            ->default('index'),
                                        Forms\Components\Select::make('robots_follow')
                                            ->label('Robots Follow')
                                            ->options([
                                                'follow' => 'Follow',
                                                'nofollow' => 'No Follow',
                                            ])
                                            ->default('follow'),
                                    ])->columns(2)
                            ->collapsible(),
                        
                        Section::make('Open Graph')
                            ->schema([
                                        Forms\Components\TextInput::make('og_title')
                                            ->label('OG Title')
                                            ->maxLength(60)
                                            ->helperText('Leave blank to use meta title')
                                            ->columnSpanFull(),
                                        Forms\Components\Textarea::make('og_description')
                                            ->label('OG Description')
                                            ->maxLength(200)
                                            ->rows(3)
                                            ->helperText('Leave blank to use meta description')
                                            ->columnSpanFull(),
                                        Forms\Components\FileUpload::make('og_image')
                                            ->label('OG Image')
                                            ->image()
                                            ->directory('seo/og')
                                            ->helperText('Leave blank to use product image')
                                            ->columnSpanFull(),
                                        Forms\Components\Select::make('og_type')
                                            ->label('OG Type')
                                            ->options([
                                                'product' => 'Product',
                                                'website' => 'Website',
                                                'article' => 'Article',
                                            ])
                                            ->default('product'),
                                    ])
                            ->collapsible(),
                        
                        Section::make('Twitter Card')
                            ->schema([
                                        Forms\Components\Select::make('twitter_card')
                                            ->label('Twitter Card Type')
                                            ->options([
                                                'summary' => 'Summary',
                                                'summary_large_image' => 'Summary Large Image',
                                                'player' => 'Player',
                                                'app' => 'App',
                                            ])
                                            ->default('summary_large_image'),
                                        Forms\Components\TextInput::make('twitter_title')
                                            ->label('Twitter Title')
                                            ->maxLength(70)
                                            ->helperText('Leave blank to use meta title')
                                            ->columnSpanFull(),
                                        Forms\Components\Textarea::make('twitter_description')
                                            ->label('Twitter Description')
                                            ->maxLength(200)
                                            ->rows(3)
                                            ->helperText('Leave blank to use meta description')
                                            ->columnSpanFull(),
                                        Forms\Components\FileUpload::make('twitter_image')
                                            ->label('Twitter Image')
                                            ->image()
                                            ->directory('seo/twitter')
                                            ->helperText('Leave blank to use product image')
                                            ->columnSpanFull(),
                                    ])
                            ->collapsible(),
                        
                        Section::make('Schema.org')
                            ->schema([
                                Forms\Components\Textarea::make('schema_markup')
                                    ->label('Custom Schema JSON-LD')
                                    ->rows(10)
                                    ->helperText('Custom JSON-LD schema. Leave blank to use auto-generated schema.')
                                    ->columnSpanFull(),
                            ])
                            ->collapsible(),
                    ])
                    ->collapsed()
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->circular()
                    ->size(50),
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Category')
                    ->badge()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_bundle')
                    ->boolean()
                    ->label('Bundle'),
                Tables\Columns\TextColumn::make('price')
                    ->money('USD')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->label('Active'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category'),
                Tables\Filters\TernaryFilter::make('is_bundle')
                    ->label('Bundle'),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
            ]);
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}

