<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostResource\Pages;
use App\Models\Post;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Illuminate\Support\Str;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Post Information')
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
                        Forms\Components\Select::make('user_id')
                            ->relationship('user', 'name')
                            ->default(fn () => auth()->id())
                            ->required(),
                        Forms\Components\Select::make('category_id')
                            ->label('Category')
                            ->relationship('category', 'name', fn ($query) => $query->where('is_active', true))
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')->required(),
                                Forms\Components\TextInput::make('slug')->required(),
                            ]),
                        Forms\Components\Select::make('status')
                            ->options([
                                'draft' => 'Draft',
                                'published' => 'Published',
                                'archived' => 'Archived',
                            ])
                            ->required()
                            ->default('draft'),
                        Forms\Components\DateTimePicker::make('published_at')
                            ->label('Publish Date'),
                        Forms\Components\Toggle::make('is_featured')
                            ->label('Featured Post')
                            ->default(false),
                        Forms\Components\Select::make('tags')
                            ->label('Tags')
                            ->relationship('tags', 'name', fn ($query) => $query->where('type', 'post')->where('is_active', true))
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')->required(),
                                Forms\Components\TextInput::make('slug')->required(),
                                Forms\Components\Select::make('type')->options(['post' => 'Post'])->default('post'),
                            ])
                            ->columnSpanFull(),
                    ])->columns(2),

                Section::make('Content')
                    ->schema([
                        Forms\Components\Textarea::make('excerpt')
                            ->rows(3)
                            ->maxLength(500),
                        Forms\Components\RichEditor::make('content')
                            ->required()
                            ->columnSpanFull(),
                    ]),

                Section::make('Media')
                    ->schema([
                        Forms\Components\FileUpload::make('featured_image')
                            ->image()
                            ->directory('posts')
                            ->visibility('public'),
                        Forms\Components\FileUpload::make('images')
                            ->image()
                            ->multiple()
                            ->directory('posts/gallery')
                            ->visibility('public'),
                    ]),

                Section::make('SEO & Meta Tags')
                    ->schema([
                        Section::make('Basic SEO')
                            ->schema([
                                Forms\Components\TextInput::make('meta_title')
                                    ->label('Meta Title')
                                    ->maxLength(60)
                                    ->helperText('Leave blank to use post title')
                                    ->columnSpanFull(),
                                Forms\Components\Textarea::make('meta_description')
                                    ->label('Meta Description')
                                    ->rows(3)
                                    ->maxLength(160)
                                    ->helperText('Leave blank to use post excerpt')
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
                                    ->rows(3)
                                    ->maxLength(200)
                                    ->helperText('Leave blank to use meta description')
                                    ->columnSpanFull(),
                                Forms\Components\FileUpload::make('og_image')
                                    ->label('OG Image')
                                    ->image()
                                    ->directory('seo/og')
                                    ->helperText('Leave blank to use featured image')
                                    ->columnSpanFull(),
                                Forms\Components\Select::make('og_type')
                                    ->label('OG Type')
                                    ->options([
                                        'article' => 'Article',
                                        'website' => 'Website',
                                        'blog' => 'Blog',
                                    ])
                                    ->default('article'),
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
                                    ->rows(3)
                                    ->maxLength(200)
                                    ->helperText('Leave blank to use meta description')
                                    ->columnSpanFull(),
                                Forms\Components\FileUpload::make('twitter_image')
                                    ->label('Twitter Image')
                                    ->image()
                                    ->directory('seo/twitter')
                                    ->helperText('Leave blank to use featured image')
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
                Tables\Columns\ImageColumn::make('featured_image')
                    ->circular()
                    ->size(50),
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->limit(50),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Author')
                    ->sortable(),
                Tables\Columns\TextColumn::make('category')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'tutorial' => 'success',
                        'news' => 'info',
                        'tips' => 'warning',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'published' => 'success',
                        'draft' => 'warning',
                        'archived' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\IconColumn::make('is_featured')
                    ->boolean()
                    ->label('Featured'),
                Tables\Columns\TextColumn::make('views')
                    ->sortable()
                    ->label('Views'),
                Tables\Columns\TextColumn::make('published_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status'),
                Tables\Filters\SelectFilter::make('category'),
                Tables\Filters\TernaryFilter::make('is_featured')->label('Featured'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }
}

