<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Tests\Fixtures;

use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Statikbe\FilamentFlexibleContentBlockPages\Models\Settings;

/**
 * A custom Settings model that extends the package's Settings model.
 * This simulates what projects would do when adding their own settings fields.
 */
class CustomSettings extends Settings
{
    const SETTING_INTRO = 'intro';

    const SETTING_SOCIAL_LINKS = 'social_links';

    const SETTING_ITEMS_PER_PAGE = 'items_per_page';

    const COLLECTION_LOGO = 'logo';

    const CONVERSION_LOGO = 'logo';

    protected $translatable = [
        parent::SETTING_FOOTER_COPYRIGHT,
        parent::SETTING_CONTACT_INFO,
        self::SETTING_INTRO,
    ];

    protected $casts = [
        self::SETTING_SOCIAL_LINKS => 'array',
        self::SETTING_ITEMS_PER_PAGE => 'integer',
    ];

    protected function registerExtraMediaCollections(): void
    {
        $this->addMediaCollection(static::COLLECTION_LOGO)
            ->singleFile()
            ->registerMediaConversions(function (Media $media) {
                $this->addMediaConversion(static::CONVERSION_LOGO)
                    ->fit(Fit::Contain, 200, 200);
            });
    }
}
