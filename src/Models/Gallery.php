<?php
/*
 * Copyright CWSPS154. All rights reserved.
 * @auth CWSPS154
 * @link  https://github.com/CWSPS154
 */

namespace CWSPS154\FilamentGallery\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Gallery extends Model implements HasMedia
{
    use HasUuids, InteractsWithMedia;

    public const DEFAULT_IMAGE_URL = 'https://cdn.pixabay.com/photo/2017/03/21/02/00/image-2160911_1280.png';

    public const DEFAULT_DATETIME_FORMAT = 'M-d-Y h:i:s A';

    /**
     * @var string[]
     */
    protected $fillable = [
        'title',
        'date',
        'external',
        'url'
    ];

    public function registerMediaConversions(Media|null $media = null): void
    {
        $this->addMediaConversion('cover')
            ->fit(Fit::Crop, 384, 384)
            ->format('webp');
        $this->addMediaConversion('thumbnail')
            ->fit(Fit::Crop, 384, 384)
            ->format('webp');
    }

    /**
     * Register media collection.
     *
     * @return void
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('cover-collection')
            ->singleFile()
            ->useFallbackUrl(self::DEFAULT_IMAGE_URL)
            ->useFallbackPath(public_path(self::DEFAULT_IMAGE_URL));

        $this->addMediaCollection('gallery-collection')
            ->useFallbackUrl(self::DEFAULT_IMAGE_URL)
            ->useFallbackPath(public_path(self::DEFAULT_IMAGE_URL));
    }
}
