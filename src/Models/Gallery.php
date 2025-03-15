<?php

/*
 * Copyright CWSPS154. All rights reserved.
 * @auth CWSPS154
 * @link  https://github.com/CWSPS154
 */

namespace CWSPS154\MyGallery\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Gallery extends Model implements HasMedia
{
    use InteractsWithMedia;

    public const DEFAULT_IMAGE_URL = 'https://cdn.pixabay.com/photo/2017/03/21/02/00/image-2160911_1280.png';

    public const DEFAULT_IMAGE_FOR_VIDEO_URL = 'https://cdn.pixabay.com/photo/2017/05/09/10/03/play-2297762_1280.png';

    public const DEFAULT_DATETIME_FORMAT = 'M-d-Y h:i:s A';

    public const GALLERY = 'gallery';

    public const VIEW_GALLERY = 'view-gallery';

    public const CREATE_GALLERY = 'create-gallery';

    public const EDIT_GALLERY = 'edit-gallery';

    public const DELETE_GALLERY = 'delete-gallery';

    /**
     * @var string[]
     */
    protected $fillable = [
        'title',
        'date',
        'external',
        'show_date_in_title',
        'title_invert',
        'url',
    ];

    protected $casts = [
        'external' => 'boolean',
        'show_date_in_title' => 'boolean',
        'title_invert' => 'boolean',
    ];

    /**
     * Get Formated title
     */
    public function getFormatedTitleAttribute(): string
    {
        if ($this->show_date_in_title) {
            if ($this->title_invert) {
                return $this->date.' - '.$this->title;
            }

            return $this->title.' - '.$this->date;
        }

        return $this->title;
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('cover')
            ->fit(Fit::Crop, 384, 384)
            ->format('webp');
        $this->addMediaConversion('thumbnail')
            ->fit(Fit::Crop, 384, 384)
            ->format('webp');
        $this->addMediaConversion('thumbnail')
            ->fit(Fit::Contain)
            ->extractVideoFrameAtSecond(5)
            ->performOnCollections('gallery-collection');
    }

    /**
     * Register media collection.
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

    /**
     * Retrieve Youtube Video Links
     */
    public function youtubeVideos(): HasMany
    {
        return $this->hasMany(YouTubeLink::class, 'gallery_id', 'id');
    }

    public static function convertToEmbedUrl(string $url): string
    {
        $parsedUrl = parse_url($url);
        if ($parsedUrl['host'] === 'youtu.be') {
            $videoId = ltrim($parsedUrl['path'], '/');
        } elseif ($parsedUrl['host'] === 'www.youtube.com' || $parsedUrl['host'] === 'youtube.com') {
            parse_str($parsedUrl['query'], $params);
            if (isset($params['v'])) {
                $videoId = $params['v'];
            } else {
                return $url;
            }
        } else {
            return $url;
        }

        return 'https://www.youtube.com/embed/'.$videoId;
    }

    public static function getYouTubeThumbnail(string $url): ?string
    {
        $parsedUrl = parse_url($url);
        if ($parsedUrl['host'] === 'youtu.be') {
            $videoId = ltrim($parsedUrl['path'], '/');
        } elseif ($parsedUrl['host'] === 'www.youtube.com' || $parsedUrl['host'] === 'youtube.com') {
            parse_str($parsedUrl['query'], $params);
            if (isset($params['v'])) {
                $videoId = $params['v'];
            } else {
                return null;
            }
        } else {
            return null;
        }

        return 'https://img.youtube.com/vi/'.$videoId.'/maxresdefault.jpg';
    }
}
