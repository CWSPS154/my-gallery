<?php
/*
 * Copyright CWSPS154. All rights reserved.
 * @auth CWSPS154
 * @link  https://github.com/CWSPS154
 */

declare(strict_types=1);

namespace CWSPS154\FilamentGallery;

use CWSPS154\FilamentGallery\Database\Seeders\DatabaseSeeder;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentGalleryServiceProvider extends PackageServiceProvider
{
    public static string $name = 'filament-gallery';

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
                    'alter_table_galleries'
                ]
            )
            ->hasInstallCommand(function (InstallCommand $command) {
                $command
                    ->startWith(function (InstallCommand $command) {
                        $command->info('Hi Mate, Thank you for installing My Package!');
                    })
                    ->publishMigrations()
                    ->askToRunMigrations()
                    ->endWith(function (InstallCommand $command) {
                        if ($command->confirm('Do you wish to run the seeder for cwsps154/filament-users-roles-permissions ?')) {
                            $command->comment('The seeder is filled with "admin" as panel id, please check the route name for your panel');
                            $command->comment('Running seeder...');
                            $command->call('db:seed', [
                                'class' => DatabaseSeeder::class
                            ]);
                        }
                        $command->info('I hope this package will help you to build a gallery manager');
                    })
                    ->askToStarRepoOnGitHub('CWSPS154/filament-gallery');
            });
    }
}
