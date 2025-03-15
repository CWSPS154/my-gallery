<?php

/*
 * Copyright CWSPS154. All rights reserved.
 * @auth CWSPS154
 * @link  https://github.com/CWSPS154
 */

declare(strict_types=1);

namespace CWSPS154\MyGallery;

use CWSPS154\MyGallery\Database\Seeders\DatabaseSeeder;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class MyGalleryServiceProvider extends PackageServiceProvider
{
    public static string $name = 'my-gallery';

    public function configurePackage(Package $package): void
    {
        $package->name(self::$name)
            ->hasViews()
            ->hasConfigFile()
            ->hasTranslations()
            ->hasMigrations(
                [
                    'create_galleries_table',
                    'create_you_tube_links_table',
                ]
            )
            ->hasInstallCommand(function (InstallCommand $command) {
                $command
                    ->startWith(function (InstallCommand $command) {
                        $command->info('Hi Mate, Thank you for installing Gallery App');
                    })
                    ->publishMigrations()
                    ->askToRunMigrations()
                    ->endWith(function (InstallCommand $command) {
                        if ($command->confirm('Are you using cwsps154/users-roles-permissions in this project?')) {
                            $command->comment('Running seeder...');
                            $command->call('db:seed', [
                                'class' => DatabaseSeeder::class,
                            ]);
                        }
                        $command->info('I hope this package will help you to build a gallery manager');
                        $command->askToStarRepoOnGitHub('CWSPS154/filament-gallery');
                    });
            });
    }
}
