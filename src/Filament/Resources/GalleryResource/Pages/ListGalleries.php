<?php

/*
 * Copyright CWSPS154. All rights reserved.
 * @auth CWSPS154
 * @link  https://github.com/CWSPS154
 */

namespace CWSPS154\MyGallery\Filament\Resources\GalleryResource\Pages;

use CWSPS154\MyGallery\Models\Gallery;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Support\Htmlable;

class ListGalleries extends ListRecords
{
    protected static string $view = 'my-gallery::filament.pages.list-galleries';

    public int|string $itemsPerPage = 20;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getViewData(): array
    {
        return [
            'galleries' => $this->getGalleries(),
        ];
    }

    public function getGalleries(): LengthAwarePaginator
    {
        return Gallery::query()
            ->latest()
            ->whereLike('title', '%'.$this->tableSearch.'%')
            ->orWhereLike('date', '%'.$this->tableSearch.'%')
            ->paginate($this->itemsPerPage);
    }

    public function getEditAction($record): Actions\EditAction
    {
        return Actions\EditAction::make()
            ->url($this->getResource()::getUrl('edit', [$record->id]))
            ->visible($this->getResource()::canEdit($record));
    }

    public function getTitle(): string|Htmlable
    {
        return __('my-gallery::gallery.gallery');
    }

    public static function getResource(): string
    {
        return static::$resource = config('my-gallery.gallery-resource');
    }
}
