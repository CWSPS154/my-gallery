<?php

/*
 * Copyright CWSPS154. All rights reserved.
 * @auth CWSPS154
 * @link  https://github.com/CWSPS154
 */

declare(strict_types=1);

namespace CWSPS154\MyGallery;

use Closure;
use Filament\Contracts\Plugin;
use Filament\Panel;
use Filament\Support\Concerns\EvaluatesClosures;

class MyGalleryPlugin implements Plugin
{
    use EvaluatesClosures;

    protected Closure|array|bool $canViewAny = true;

    protected Closure|array|bool $canCreate = true;

    protected Closure|array|bool $canEdit = true;

    protected Closure|array|bool $canDelete = true;

    public function getId(): string
    {
        return MyGalleryServiceProvider::$name;
    }

    public function register(Panel $panel): void
    {
        $panel->discoverResources(
            in: __DIR__.'/Filament/Resources',
            for: 'CWSPS154\\MyGallery\\Filament\\Resources'
        );
    }

    public function boot(Panel $panel): void
    {
        // TODO: Implement boot() method.
    }

    public static function make(): static
    {
        return app(static::class);
    }

    protected function setAbility(mixed $ability, mixed $arguments = null): array|bool
    {
        if ($ability instanceof Closure) {
            return $this->evaluate($ability);
        }

        if (is_string($ability) && ! is_null($arguments)) {
            return [
                'ability' => $ability,
                'arguments' => $arguments,
            ];
        }

        return (bool) $ability;
    }

    public function canViewAny(bool|Closure|string $ability = true, $arguments = null): static
    {
        $this->canViewAny = $this->setAbility($ability, $arguments);

        return $this;
    }

    public function getCanViewAny(): array|bool
    {
        return $this->canViewAny;
    }

    public function canCreate(bool|Closure|string $ability = true, $arguments = null): static
    {
        $this->canCreate = $this->setAbility($ability, $arguments);

        return $this;
    }

    public function getCanCreate(): array|bool
    {
        return $this->canCreate;
    }

    public function canEdit(bool|Closure|string $ability = true, $arguments = null): static
    {
        $this->canEdit = $this->setAbility($ability, $arguments);

        return $this;
    }

    public function getCanEdit(): array|bool
    {
        return $this->canEdit;
    }

    public function canDelete(bool|Closure|string $ability = true, $arguments = null): static
    {
        $this->canDelete = $this->setAbility($ability, $arguments);

        return $this;
    }

    public function getCanDelete(): array|bool
    {
        return $this->canDelete;
    }
}
