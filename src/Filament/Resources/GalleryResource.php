<?php

/*
 * Copyright CWSPS154. All rights reserved.
 * @auth CWSPS154
 * @link  https://github.com/CWSPS154
 */

namespace CWSPS154\MyGallery\Filament\Resources;

use CWSPS154\MyGallery\Jobs\SaveGalleryImagesJob;
use CWSPS154\MyGallery\Models\Gallery;
use CWSPS154\MyGallery\MyGalleryServiceProvider;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;

class GalleryResource extends Resource
{
    protected static ?string $model = Gallery::class;

    protected static ?string $navigationIcon = 'heroicon-o-photo';

    protected static ?string $cluster = null;

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make()
                            ->schema([
                                SpatieMediaLibraryFileUpload::make('items.media')
                                    ->collection('gallery-collection')
                                    ->conversion('thumbnail')
                                    ->acceptedFileTypes(['image/*', 'video/*'])
                                    ->multiple()
                                    ->reorderable()
                                    ->appendFiles()
                                    ->panelLayout('grid')
                                    ->maxSize(10240)
                                    ->maxFiles(20)
                                    ->label(__('my-gallery::gallery.images.&.videos'))
                                    ->helperText(__('my-gallery::gallery.images.&.videos.helper.text'))
                                    ->columnSpanFull()
                                    ->optimize('webp')
                                    ->saveUploadedFileUsing(function ($file, $state, $set, $record) {
                                        $filePath = $file->getRealPath();
                                        SaveGalleryImagesJob::dispatch($record, $filePath, 'gallery-collection');
                                    }),
                                Repeater::make('youtubeVideos')
                                    ->label(__('my-gallery::gallery.youtube.links'))
                                    ->relationship('youtubeVideos')
                                    ->schema([
                                        Forms\Components\TextInput::make('url')
                                            ->label(__('my-gallery::gallery.links'))
                                            ->url()
                                            ->maxLength(255),
                                    ])->defaultItems(0),
                            ])->visible(function (Get $get) {
                                if ($get('external')) {
                                    return false;
                                }

                                return true;
                            }),
                        Forms\Components\Section::make()
                            ->schema([
                                Forms\Components\TextInput::make('url')
                                    ->label(__('my-gallery::gallery.url'))
                                    ->url()
                                    ->required()
                                    ->maxLength(255),
                            ])->visible(function (Get $get) {
                                if ($get('external')) {
                                    return true;
                                }

                                return false;
                            }),
                    ])->columnSpan(['lg' => 2]),
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make()
                            ->schema([
                                Forms\Components\Toggle::make('external')
                                    ->label(__('my-gallery::gallery.external.link'))
                                    ->helperText(__('my-gallery::gallery.external.link.helper.text'))
                                    ->live()
                                    ->default(false),
                                Forms\Components\TextInput::make('title')
                                    ->label(__('my-gallery::gallery.title'))
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\DatePicker::make('date')
                                    ->label(__('my-gallery::gallery.date'))
                                    ->native(false)
                                    ->placeholder('DD/MM/YY')
                                    ->displayFormat('d/m/Y')
                                    ->weekStartsOnSunday()
                                    ->closeOnDateSelection()
                                    ->default(now())
                                    ->required(),
                                SpatieMediaLibraryFileUpload::make('media')
                                    ->collection('cover-collection')
                                    ->conversion('cover')
                                    ->image()
                                    ->maxSize(10240)
                                    ->label(__('my-gallery::gallery.cover.image'))
                                    ->columnSpanFull()
                                    ->optimize('webp')
                                    ->saveUploadedFileUsing(function ($file, $state, $set, $record) {
                                        $filePath = $file->getRealPath();
                                        SaveGalleryImagesJob::dispatch($record, $filePath, 'cover-collection');
                                    }),
                                Forms\Components\Split::make([
                                    Forms\Components\Toggle::make('show_date_in_title')
                                        ->label(__('my-gallery::gallery.date.show'))
                                        ->live()
                                        ->afterStateUpdated(function (Get $get, $state, Forms\Set $set) {
                                            if (! $get('show_date_in_title')) {
                                                $set('title_invert', false);
                                            }
                                        })
                                        ->default(true),
                                    Forms\Components\Toggle::make('title_invert')
                                        ->label(__('my-gallery::gallery.title.invert'))
                                        ->helperText(__('my-gallery::gallery.title.invert.helper.text'))
                                        ->live()
                                        ->afterStateUpdated(function (Get $get, $state, Forms\Set $set) {
                                            if ($get('title_invert')) {
                                                $set('show_date_in_title', true);
                                            }
                                        }),
                                ]),
                            ]),
                    ])->columnSpan(['lg' => 1]),
            ])->columns(3);
    }

    public static function getPages(): array
    {
        return [
            'index' => config('my-gallery.gallery-resource-pages.list')::route('/'),
            'create' => config('my-gallery.gallery-resource-pages.create')::route('/create'),
            'edit' => config('my-gallery.gallery-resource-pages.edit')::route('/{record}/edit'),
        ];
    }

    public static function getNavigationLabel(): string
    {
        return __('my-gallery::gallery.gallery');
    }

    public static function getBreadcrumb(): string
    {
        return __('my-gallery::gallery.gallery');
    }

    public static function getNavigationIcon(): string|Htmlable|null
    {
        return 'heroicon-o-photo';
    }

    public static function getNavigationGroup(): ?string
    {
        return __('my-gallery::gallery.group');
    }

    public static function getNavigationSort(): ?int
    {
        return 100;
    }

    public static function checkAccess(string $method, ?Model $record = null): bool
    {
        $plugin = Filament::getCurrentPanel()?->getPlugin(MyGalleryServiceProvider::$name);
        $access = $plugin->$method();
        if (! empty($access) && is_array($access) && isset($access['ability'], $access['arguments'])) {
            return Gate::allows($access['ability'], $access['arguments']);
        }

        return $access;
    }

    public static function canViewAny(): bool
    {
        return self::checkAccess('getCanViewAny');
    }

    public static function canCreate(): bool
    {
        return self::checkAccess('getCanCreate');
    }

    public static function canEdit(Model $record): bool
    {
        return self::checkAccess('getCanEdit', $record);
    }

    public static function canDelete(Model $record): bool
    {
        return self::checkAccess('getCanDelete', $record);
    }
}
