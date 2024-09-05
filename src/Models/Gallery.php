<?php
/*
 * Copyright CWSPS154. All rights reserved.
 * @auth CWSPS154
 * @link  https://github.com/CWSPS154
 */

namespace CWSPS154\FilamentGallery\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
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

    /**
     * Get Formated title
     *
     * @return string
     */
    public function getFormatedTitleAttribute(): string
    {
        return $this->date.' - '.$this->title;
    }

    /**
     * @param Media|null $media
     * @return void
     */
    public function registerMediaConversions(Media|null $media = null): void
    {
        $this->addMediaConversion('cover')
            ->fit(Fit::Crop, 384, 384)
            ->format('webp');
        $this->addMediaConversion('thumbnail')
            ->fit(Fit::Crop, 384, 384)
            ->format('webp');
        $this->addMediaConversion('thumbnail')
            ->width(368)
            ->height(232)
            ->extractVideoFrameAtSecond(5)
            ->performOnCollections('gallery-collection');
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

    /**
     * Retrieve Youtube Video Links
     *
     * @return HasMany
     */
    public function youtubeVideos(): HasMany
    {
        return $this->hasMany(YouTubeLink::class, 'gallery_id', 'id');
    }

    /**
     * @param string $url
     * @return string
     */
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
        return 'https://www.youtube.com/embed/' . $videoId;
    }

    /**
     * @param string $url
     * @return string|null
     */
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
        return 'https://img.youtube.com/vi/' . $videoId . '/maxresdefault.jpg';
    }
}
