<?php

namespace App\Filament\Resources\SettingResource\Pages;

use App\Filament\Resources\SettingResource;
use App\Models\Setting;
use Filament\Actions;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;

class ManageSettings extends ManageRecords
{
    protected static string $resource = SettingResource::class;

    protected string $view = 'filament.pages.manage-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $this->fillForm();
    }

    protected function fillForm(): void
    {
        $settings = Setting::pluck('value', 'key')->toArray();
        
        // File upload fields
        $fileFields = ['favicon', 'apple_touch_icon', 'favicon_32', 'favicon_16', 'logo', 'logo_dark', 
                      'og_default_image', 'twitter_default_image', 'organization_logo'];
        
        $formData = [];
        foreach ($settings as $key => $value) {
            // For file fields, keep as string path
            if (in_array($key, $fileFields)) {
                $formData[$key] = is_string($value) && !empty($value) ? $value : null;
            } else {
                // For other fields, ensure string or null
                $formData[$key] = $value !== null ? (string) $value : null;
            }
        }
        
        $this->form->fill($formData);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('General SEO')
                    ->schema([
                        Forms\Components\TextInput::make('site_name')->label('Site Name')->maxLength(100),
                        Forms\Components\TextInput::make('site_tagline')->label('Site Tagline')->maxLength(200),
                        Forms\Components\TextInput::make('default_meta_title')->label('Default Meta Title')->maxLength(60),
                        Forms\Components\Textarea::make('default_meta_description')->label('Default Meta Description')->maxLength(155)->rows(3),
                        Forms\Components\Select::make('robots_index')->label('Default Robots Index')->options(['index' => 'Index', 'noindex' => 'No Index'])->default('index'),
                        Forms\Components\Select::make('robots_follow')->label('Default Robots Follow')->options(['follow' => 'Follow', 'nofollow' => 'No Follow'])->default('follow'),
                    ])->columns(2),

                Section::make('Social Media')
                    ->schema([
                        Forms\Components\TextInput::make('twitter_username')->label('Twitter Username')->placeholder('@yourhandle')->maxLength(50),
                        Forms\Components\TextInput::make('facebook_app_id')->label('Facebook App ID')->maxLength(50),
                        Forms\Components\FileUpload::make('og_default_image')->label('Default Open Graph Image')->image()->directory('seo'),
                        Forms\Components\FileUpload::make('twitter_default_image')->label('Default Twitter Card Image')->image()->directory('seo'),
                        Forms\Components\TextInput::make('og_site_name')->label('Open Graph Site Name')->maxLength(100),
                    ]),

                Section::make('Favicons & Logo')
                    ->schema([
                        Forms\Components\FileUpload::make('favicon')->label('Favicon (.ico)')->directory('favicons'),
                        Forms\Components\FileUpload::make('apple_touch_icon')->label('Apple Touch Icon (180x180)')->image()->directory('favicons'),
                        Forms\Components\FileUpload::make('favicon_32')->label('Favicon 32x32')->image()->directory('favicons'),
                        Forms\Components\FileUpload::make('favicon_16')->label('Favicon 16x16')->image()->directory('favicons'),
                        Forms\Components\FileUpload::make('logo')->label('Logo')->image()->directory('logo'),
                        Forms\Components\FileUpload::make('logo_dark')->label('Logo (Dark Mode)')->image()->directory('logo'),
                    ])->columns(2),

                Section::make('Schema.org')
                    ->schema([
                        Forms\Components\TextInput::make('organization_name')->label('Organization Name')->maxLength(100),
                        Forms\Components\FileUpload::make('organization_logo')->label('Organization Logo')->image()->directory('seo'),
                        Forms\Components\TextInput::make('organization_url')->label('Organization URL')->url(),
                    ]),

                Section::make('Tracking & Analytics')
                    ->schema([
                        Forms\Components\TextInput::make('google_analytics_id')->label('Google Analytics ID')->placeholder('G-XXXXXXXXXX')->maxLength(50),
                        Forms\Components\TextInput::make('google_tag_manager_id')->label('Google Tag Manager ID')->placeholder('GTM-XXXXXXX')->maxLength(50),
                        Forms\Components\TextInput::make('google_site_verification')->label('Google Site Verification Code')->maxLength(100),
                        Forms\Components\TextInput::make('facebook_pixel_id')->label('Facebook Pixel ID')->maxLength(50),
                        Forms\Components\TextInput::make('hotjar_id')->label('Hotjar ID')->maxLength(50),
                    ])->columns(2),

                Section::make('Payment')
                    ->schema([
                        Forms\Components\TextInput::make('stripe_key')->label('Stripe Key')->placeholder('sk_test_... or sk_live_...')->maxLength(200),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        try {
            $data = $this->form->getState();
            
            \Log::info('Saving settings', ['data_count' => count($data)]);

            foreach ($data as $key => $value) {
                // Handle file uploads
                if (is_array($value)) {
                    if (!empty($value)) {
                        $value = $value[0]; // Get first file path
                    } else {
                        continue; // Skip empty arrays
                    }
                }
                
                // Convert to string if not null
                if ($value !== null && !is_string($value)) {
                    $value = (string) $value;
                }
                
                Setting::updateOrCreate(
                    ['key' => $key],
                    ['value' => $value]
                );
                
                \Log::info("Saved: {$key} = " . ($value ?? 'null'));
            }

            Setting::clearCache();

            Notification::make()
                ->success()
                ->title('Settings saved!')
                ->body(count($data) . ' settings updated successfully')
                ->send();
                
            // Reload form data
            $this->fillForm();
            
        } catch (\Exception $e) {
            \Log::error('Settings save error', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            
            Notification::make()
                ->danger()
                ->title('Error saving settings')
                ->body($e->getMessage())
                ->send();
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('save')
                ->label('Save Settings')
                ->action('save')
                ->color('primary')
                ->icon('heroicon-o-check-circle')
                ->requiresConfirmation(false),
        ];
    }
}
