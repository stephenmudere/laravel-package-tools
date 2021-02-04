<?php

namespace Stephenmudere\LaravelPackageTools;

use Illuminate\Support\Str;

class Package
{
    public  $name;

    public  $configFileName = null;

    public  $hasViews = false;

    public  $hasTranslations = false;

    public  $hasAssets = false;

    public  $migrationFileNames = [];

    public  $routeFileNames = [];

    public  $commands = [];

    public  $basePath;

    public $namespace;

    public function name( $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function namespace( $namespace): self
    {
        $this->namespace = $namespace;

        return $this;
    }

    

    public function hasConfigFile( $configFileName = null): self
    {
        $this->configFileName = $configFileName ?? $this->shortName();

        return $this;
    }

    public function hasViews(): self
    {
        $this->hasViews = true;

        return $this;
    }

    public function hasTranslations(): self
    {
        $this->hasTranslations = true;

        return $this;
    }

    public function hasAssets(): self
    {
        $this->hasAssets = true;

        return $this;
    }

    public function hasMigration( $migrationFileName): self
    {
        $this->migrationFileNames[] = $migrationFileName;

        return $this;
    }

    public function hasMigrations( $migrationFileNames): self
    {
        $this->migrationFileNames = array_merge($this->migrationFileNames, $migrationFileNames);

        return $this;
    }

    public function hasCommand( $commandClassName): self
    {
        $this->commands[] = $commandClassName;

        return $this;
    }

    public function hasCommands( $commandClassNames): self
    {
        $this->commands = array_merge($this->commands, $commandClassNames);

        return $this;
    }

    public function hasRoute( $routeFileName): self
    {
        $this->routeFileNames[] = $routeFileName;

        return $this;
    }

    public function hasRoutes( $routeFileNames): self
    {
        $this->routeFileNames = array_merge($this->routeFileNames, $routeFileNames);

        return $this;
    }

    public function basePath($directory = null): string
    {
        if ($directory === null) {
            return $this->basePath;
        }

        return $this->basePath . DIRECTORY_SEPARATOR . ltrim($directory, DIRECTORY_SEPARATOR);
    }

    public function setBasePath( $path): self
    {
        $this->basePath = $path;

        return $this;
    }

    public function shortName(): string
    {
        return Str::after($this->name, 'laravel-');
    }
}
