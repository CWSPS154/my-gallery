<?php
/*
 * Copyright CWSPS154. All rights reserved.
 * @auth CWSPS154
 * @link  https://github.com/CWSPS154
 */

namespace CWSPS154\FilamentGallery\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Spatie\MediaLibrary\HasMedia;
use Throwable;

class SaveGalleryImagesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public HasMedia $model;

    /**
     * @var mixed
     */
    public mixed $filePath;

    /**
     * @var mixed|string
     */
    public mixed $collection;


    /**
     * Create a new job instance.
     */
    public function __construct(HasMedia $model, $filePath, $collection = 'default')
    {
        $this->model = $model;
        $this->filePath = $filePath;
        $this->collection = $collection;
    }

    /**
     * Execute the job.
     *
     * @throws Throwable
     */
    public function handle(): void
    {
        $this->model->addMedia($this->filePath)
            ->toMediaCollection($this->collection);
    }
}
