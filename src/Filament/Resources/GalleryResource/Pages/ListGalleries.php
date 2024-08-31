<?php
/*
 * Copyright CWSPS154. All rights reserved.
 * @auth CWSPS154
 * @link  https://github.com/CWSPS154
 */

namespace CWSPS154\FilamentGallery\Filament\Resources\GalleryResource\Pages;

use CWSPS154\FilamentGallery\Filament\Resources\GalleryResource;
use CWSPS154\FilamentGallery\Models\Gallery;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Support\Htmlable;

class ListGalleries extends ListRecords
{
    protected static string $view = 'filament-gallery::filament.pages.list-galleries';

    protected static string $resource = GalleryResource::class;

    /**
     * @var int|string
     */
    public int|string $itemsPerPage = 20;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getViewData() : array
    {
        return [
            'galleries' => $this->getGalleries(),
        ];
    }

    public function getGalleries(): LengthAwarePaginator
    {
        return Gallery::query()
            ->latest()
            ->whereLike('title','%'.$this->tableSearch.'%')
            ->orWhereLike('date','%'.$this->tableSearch.'%')
            ->paginate($this->itemsPerPage);
    }

    public function getEditAction($record): Actions\EditAction
    {
        return Actions\EditAction::make()
            ->url(GalleryResource::getUrl('edit',[$record->id]));
    }

    public function getTitle(): string|Htmlable
    {
        return __(config('filament-gallery.navigation.breadcrumb'));
    }
}
