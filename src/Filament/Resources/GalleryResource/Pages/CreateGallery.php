<?php

/*
 * Copyright CWSPS154. All rights reserved.
 * @auth CWSPS154
 * @link  https://github.com/CWSPS154
 */

namespace CWSPS154\MyGallery\Filament\Resources\GalleryResource\Pages;

use Filament\Resources\Pages\CreateRecord;

class CreateGallery extends CreateRecord
{
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    public static function getResource(): string
    {
        return static::$resource = config('my-gallery.gallery-resource');
    }
}
