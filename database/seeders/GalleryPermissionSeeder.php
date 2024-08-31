<?php
/*
 * Copyright CWSPS154. All rights reserved.
 * @auth CWSPS154
 * @link  https://github.com/CWSPS154
 */

namespace CWSPS154\FilamentGallery\Database\Seeders;

use CWSPS154\FilamentUsersRolesPermissions\Models\Permission;
use Illuminate\Database\Seeder;

class GalleryPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $id = Permission::create([
            'name' => 'Gallery',
            'identifier' => 'gallery',
            'route' => null,
            'parent_id' => null,
            'status' => true
        ])->id;

        Permission::create([
            'name' => 'View Gallery',
            'identifier' => 'view-gallery',
            'route' => 'filament.admin.resources.galleries.index',
            'parent_id' => $id,
            'status' => true
        ]);

        Permission::create([
            'name' => 'Create Gallery',
            'identifier' => 'create-gallery',
            'route' => 'filament.admin.resources.galleries.create',
            'parent_id' => $id,
            'status' => true
        ]);

        Permission::create([
            'name' => 'Edit Gallery',
            'identifier' => 'edit-gallery',
            'route' => 'filament.admin.resources.galleries.edit',
            'parent_id' => $id,
            'status' => true
        ]);

        Permission::create([
            'name' => 'Delete Gallery',
            'identifier' => 'delete-gallery',
            'route' => null,
            'parent_id' => $id,
            'status' => true
        ]);
    }
}
