<?php

/*
 * Copyright CWSPS154. All rights reserved.
 * @auth CWSPS154
 * @link  https://github.com/CWSPS154
 */

use CWSPS154\MyGallery\Models\Gallery;
use CWSPS154\MyGallery\MyGalleryServiceProvider;
use Filament\Facades\Filament;

$panel_ids = [];

foreach (Filament::getPanels() as $panel) {
    if ($panel->hasPlugin(MyGalleryServiceProvider::$name)) {
        $panel_ids[] = $panel->getId();
    }
}

return [
    Gallery::GALLERY => [
        'name' => 'Gallery',
        'panel_ids' => $panel_ids,
        'route' => null,
        'status' => true,
        'children' => [
            Gallery::VIEW_GALLERY => [
                'name' => 'View Gallery',
                'panel_ids' => $panel_ids,
                'route' => 'resources.galleries.index',
                'status' => true,
            ],
            Gallery::CREATE_GALLERY => [
                'name' => 'Create Gallery',
                'panel_ids' => $panel_ids,
                'route' => 'resources.galleries.create',
                'status' => true,
            ],
            Gallery::EDIT_GALLERY => [
                'name' => 'Edit Gallery',
                'panel_ids' => $panel_ids,
                'route' => 'resources.galleries.edit',
                'status' => true,
            ],
            Gallery::DELETE_GALLERY => [
                'name' => 'Delete Gallery',
                'panel_ids' => $panel_ids,
                'route' => null,
                'status' => true,
            ],
        ],
    ],
];
