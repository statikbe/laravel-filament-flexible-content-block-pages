---
name: add-flexible-pages-setting
description: Add a new CMS setting field (text, number, toggle, translatable field or image) to the settings of statikbe/laravel-filament-flexible-content-block-pages, including the migration, the extended Settings model, the Filament form field and reading the value in the frontend.
---

# Adding a settings field to Flexible Content Block Pages

## When to use this skill

Use this skill when a project needs an extra CMS-managed setting, e.g. a phone number, a
social media link, a default share image, a logo, an announcement bar text, a number of items
per page. Signs you need it: "add a setting for X", "the client must be able to change X in the
CMS", "make X configurable in the admin".

Do **not** use it for values that never change per environment or are not edited by a content
manager: those belong in a config file.

## How settings work in this package

- All settings live in **one row** of the settings table, one **column per setting**, so every
  value keeps its own type and can be cast.
- Images are **not** columns: every image setting is its own media library collection.
- Values are read through `flexiblePagesSetting()` and cached forever, until the settings are
  saved or its media changes.
- The package model `Statikbe\FilamentFlexibleContentBlockPages\Models\Settings` and resource
  `SettingsResource` are extended in the app and registered in the config. Never edit the
  package itself.

## Ask before you start

Getting these wrong means a second migration and a second review round, so make sure you know
them **before writing any code**. Ask the user about anything the request does not already make
clear:

1. **Is the field translatable?** Translatable fields need a `json` column and an entry in
   `$translatable`. Prose that a visitor reads (an intro text, a copyright line, a CTA label) is
   usually translatable. An email address, a URL, a phone number, a toggle or a number usually is
   not. When the field type makes the answer obvious, just decide and say what you decided.
2. **Which tab does it belong in?** `General`, `SEO`, or a new tab (ask for its label). If the
   request already names a group of related settings, propose a new tab.

## Steps

Check first whether the app already extends the settings model and resource (look at
`config/filament-flexible-content-block-pages.php`, keys `models.settings` and
`resources.settings`). If it does, add to those classes. If not, create them and register them.

### 1. Migration

Add a column to the existing settings table. It must be `nullable()` or have a default, because a
settings row already exists.

```php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Statikbe\FilamentFlexibleContentBlockPages\Facades\FilamentFlexibleContentBlockPages;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table(FilamentFlexibleContentBlockPages::config()->getSettingsTable(), function (Blueprint $table) {
            $table->string('company_email')->nullable();   // plain field
            $table->json('intro_text')->nullable();        // translatable field: always json
        });
    }

    public function down(): void
    {
        Schema::table(FilamentFlexibleContentBlockPages::config()->getSettingsTable(), function (Blueprint $table) {
            $table->dropColumn(['company_email', 'intro_text']);
        });
    }
};
```

Image settings need **no column**.

### 2. Extend the Settings model

```php
namespace App\Models;

use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Statikbe\FilamentFlexibleContentBlockPages\Models\Settings;

class CmsSettings extends Settings
{
    const SETTING_COMPANY_EMAIL = 'company_email';

    const SETTING_INTRO_TEXT = 'intro_text';

    const COLLECTION_LOGO = 'logo';

    const CONVERSION_LOGO = 'logo';

    // Repeat the parent fields: redeclaring the property replaces the parent list.
    protected $translatable = [
        parent::SETTING_FOOTER_COPYRIGHT,
        parent::SETTING_CONTACT_INFO,
        self::SETTING_INTRO_TEXT,
    ];

    protected $casts = [
        'items_per_page' => 'integer',
    ];

    // Only for image settings. Do NOT override registerMediaCollections(), that would drop the
    // media collections of the package.
    protected function registerExtraMediaCollections(): void
    {
        $this->addMediaCollection(static::COLLECTION_LOGO)
            ->singleFile()
            ->registerMediaConversions(function (Media $media) {
                $this->addMediaConversion(static::CONVERSION_THUMB)
                    ->fit(Fit::Contain, 400, 400);
                $this->addMediaConversion(static::CONVERSION_LOGO)
                    ->fit(Fit::Contain, 300, 300);
            });
    }
}
```

Always add a `SETTING_*` or `COLLECTION_*` constant: the rest of the app refers to the setting
through it, never through a raw string.

### 3. Add the form field

The form fields live in the **form schema class**, not in the resource:
`Statikbe\FilamentFlexibleContentBlockPages\Resources\SettingsResource\Schemas\SettingsFormSchema`.

- to add a field to an existing tab, override `getGeneralTabFormSchema()` or `getSeoTabFormSchema()`
  and merge with `parent::`;
- to add a new tab, override `getExtraFormTabs()`.

```php
namespace App\Filament\Resources\Settings\Schemas;

use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Support\Icons\Heroicon;
use App\Models\CmsSettings;
use Statikbe\FilamentFlexibleContentBlockPages\Resources\SettingsResource\Schemas\SettingsFormSchema;

class CmsSettingsFormSchema extends SettingsFormSchema
{
    public static function getGeneralTabFormSchema(): array
    {
        return array_merge(parent::getGeneralTabFormSchema(), [
            TextInput::make(CmsSettings::SETTING_COMPANY_EMAIL)
                ->label(__('cms.settings.company_email'))
                ->email(),
        ]);
    }

    public static function getExtraFormTabs(): array
    {
        return [
            Tab::make(__('cms.settings.tab_branding'))->schema([
                // a translatable field always gets the translation hint:
                TextInput::make(CmsSettings::SETTING_INTRO_TEXT)
                    ->label(__('cms.settings.intro_text'))
                    ->hint(flexiblePagesTrans('settings.translatable_field_hint'))
                    ->hintIcon(Heroicon::Language),

                SpatieMediaLibraryFileUpload::make(CmsSettings::COLLECTION_LOGO)
                    ->label(__('cms.settings.logo'))
                    ->collection(CmsSettings::COLLECTION_LOGO)
                    ->conversion(CmsSettings::CONVERSION_THUMB)
                    ->maxFiles(1),
            ]),
        ];
    }
}
```

Labels go in the app's own translation files, never hardcoded.

### 4. Extend the resource and point it at the schema

```php
namespace App\Filament\Resources\Settings;

use Filament\Schemas\Schema;
use App\Filament\Resources\Settings\Schemas\CmsSettingsFormSchema;
use Statikbe\FilamentFlexibleContentBlockPages\Resources\SettingsResource;

class CmsSettingsResource extends SettingsResource
{
    public static function form(Schema $schema): Schema
    {
        return CmsSettingsFormSchema::configure($schema);
    }
}
```

### 5. Register model and resource in the config

In `config/filament-flexible-content-block-pages.php`:

```php
'models' => [
    // ...
    'settings' => \App\Models\CmsSettings::class,
],

'resources' => [
    // ...
    'settings' => \App\Filament\Resources\Settings\CmsSettingsResource::class,
],
```

Without this the package keeps using its own model and resource, and nothing you added shows up.

### 6. Read the setting

```php
// plain, translatable and cast fields:
$email = flexiblePagesSetting(CmsSettings::SETTING_COMPANY_EMAIL, default: 'info@example.com');
$intro = flexiblePagesSetting(CmsSettings::SETTING_INTRO_TEXT);          // current locale
$introFr = flexiblePagesSetting(CmsSettings::SETTING_INTRO_TEXT, 'fr');  // explicit locale

// images:
$logoUrl = flexiblePagesSettingImageUrl(CmsSettings::COLLECTION_LOGO, CmsSettings::CONVERSION_LOGO);
$logoHtml = CmsSettings::imageHtml(CmsSettings::COLLECTION_LOGO, CmsSettings::CONVERSION_LOGO);
```

### 7. Finish

- Run the migration.
- If the setting needs a starting value, set it in a seeder or update the existing row; the
  package seeder only fills its own fields.
- Added a conversion to an existing collection? Run `php artisan media-library:regenerate`.
- Tell the user which field was added, in which tab, and whether it is translatable.

## Gotchas

- `getExtraFormTabs()` and the tab schema methods are `public static` on the **form schema**
  class. Putting them on the resource does nothing, the fields silently never appear.
- Redeclaring `$translatable` without the parent's fields makes `footer_copyright` and
  `contact_info` stop being translatable.
- Never override `registerMediaCollections()`, use `registerExtraMediaCollections()`.
- A new non-nullable column without a default breaks the existing settings row.
- Settings are cached forever per locale and flushed when the settings or their media are saved.
  If a value seems stale in development, run `php artisan cache:clear`.
