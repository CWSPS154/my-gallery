<?php
/*
 * Copyright CWSPS154. All rights reserved.
 * @auth CWSPS154
 * @link  https://github.com/CWSPS154
 */

namespace CWSPS154\FilamentGallery\Filament\Resources;

use CWSPS154\FilamentGallery\Filament\Resources\GalleryResource\Pages;
use CWSPS154\FilamentGallery\FilamentGalleryServiceProvider;
use CWSPS154\FilamentGallery\Jobs\SaveGalleryImagesJob;
use CWSPS154\FilamentGallery\Models\Gallery;
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
                                    ->label(__('filament-gallery::gallery.images.&.videos'))
                                    ->helperText(__('filament-gallery::gallery.images.&.videos.helper.text'))
                                    ->columnSpanFull()
                                    ->optimize('webp')
                                    ->saveUploadedFileUsing(function ($file, $state, $set, $record) {
                                        $filePath = $file->getRealPath();
                                        SaveGalleryImagesJob::dispatch($record, $filePath, 'gallery-collection');
                                    }),
                                Repeater::make('youtubeVideos')
                                    ->label(__('filament-gallery::gallery.youtube.links'))
                                    ->relationship('youtubeVideos')
                                    ->schema([
                                        Forms\Components\TextInput::make('url')
                                            ->label(__('filament-gallery::gallery.links'))
                                            ->url()
                                            ->maxLength(255)
                                    ])->defaultItems(0)
                            ])->visible(function (Get $get) {
                                if ($get('external')) {
                                    return false;
                                }
                                return true;
                            }),
                        Forms\Components\Section::make()
                            ->schema([
                                Forms\Components\TextInput::make('url')
                                    ->label(__('filament-gallery::gallery.url'))
                                    ->url()
                                    ->required()
                                    ->maxLength(255),
                            ])->visible(function (Get $get) {
                                if ($get('external')) {
                                    return true;
                                }
                                return false;
                            })
                    ])->columnSpan(['lg' => 2]),
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make()
                            ->schema([
                                Forms\Components\Toggle::make('external')
                                    ->label(__('filament-gallery::gallery.external.link'))
                                    ->helperText(__('filament-gallery::gallery.external.link.helper.text'))
                                    ->live()
                                    ->default(false),
                                Forms\Components\TextInput::make('title')
                                    ->label(__('filament-gallery::gallery.title'))
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\DatePicker::make('date')
                                    ->label(__('filament-gallery::gallery.date'))
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
                                    ->label(__('filament-gallery::gallery.cover.image'))
                                    ->columnSpanFull()
                                    ->optimize('webp')
                                    ->saveUploadedFileUsing(function ($file, $state, $set, $record) {
                                        $filePath = $file->getRealPath();
                                        SaveGalleryImagesJob::dispatch($record, $filePath, 'cover-collection');
                                    }),
                                Forms\Components\Split::make([
                                    Forms\Components\Toggle::make('show_date_in_title')
                                        ->label(__('filament-gallery::gallery.date.show'))
                                        ->live()
                                        ->afterStateUpdated(function (Get $get, $state, Forms\Set $set) {
                                            if (!$get('show_date_in_title')) {
                                                $set('title_invert', false);
                                            }
                                        })
                                        ->default(true),
                                    Forms\Components\Toggle::make('title_invert')
                                        ->label(__('filament-gallery::gallery.title.invert'))
                                        ->helperText(__('filament-gallery::gallery.title.invert.helper.text'))
                                        ->live()
                                        ->afterStateUpdated(function (Get $get, $state, Forms\Set $set) {
                                            if ($get('title_invert')) {
                                                $set('show_date_in_title', true);
                                            }
                                        }),
                                ]),
                            ])
                    ])->columnSpan(['lg' => 1])
            ])->columns(3);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGalleries::route('/'),
            'create' => Pages\CreateGallery::route('/create'),
            'edit' => Pages\EditGallery::route('/{record}/edit'),
        ];
    }

    public function getLayout(): string
    {
        if (config('filament-gallery.layout')) {
            return config('filament-gallery.layout');
        }
        return parent::getLayout();
    }

    public static function getCluster(): ?string
    {
        return config('filament-gallery.cluster');
    }

    public static function getNavigationLabel(): string
    {
        return __(config('filament-gallery.navigation.label'));
    }

    public static function getBreadcrumb(): string
    {
        return __(config('filament-gallery.navigation.title'));
    }

    public static function getNavigationIcon(): string|Htmlable|null
    {
        return config('filament-gallery.navigation.icon');
    }

    public static function getNavigationGroup(): ?string
    {
        return __(config('filament-gallery.navigation.group'));
    }

    public static function getNavigationSort(): ?int
    {
        return config('filament-gallery.navigation.sort');
    }

    public static function checkAccess(string $method, Model $record = null): bool
    {
        $plugin = Filament::getCurrentPanel()?->getPlugin(FilamentGalleryServiceProvider::$name);
        $access = $plugin->$method();
        if (!empty($access) && is_array($access) && isset($access['ability'], $access['arguments'])) {
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
