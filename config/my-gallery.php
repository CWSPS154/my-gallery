<?php

/*
 * Copyright CWSPS154. All rights reserved.
 * @auth CWSPS154
 * @link  https://github.com/CWSPS154
 */

return [
    'gallery-resource' => \CWSPS154\MyGallery\Filament\Resources\GalleryResource::class,
    'gallery-resource-pages' => [
        'list' => \CWSPS154\MyGallery\Filament\Resources\GalleryResource\Pages\ListGalleries::class,
        'create' => \CWSPS154\MyGallery\Filament\Resources\GalleryResource\Pages\CreateGallery::class,
        'edit' => \CWSPS154\MyGallery\Filament\Resources\GalleryResource\Pages\EditGallery::class,
    ],
];
