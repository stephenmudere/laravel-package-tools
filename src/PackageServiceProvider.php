<?php


namespace Stephenmudere\LaravelPackageTools;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use ReflectionClass;
use Stephenmudere\LaravelPackageTools\Exceptions\InvalidPackage;

abstract class PackageServiceProvider extends ServiceProvider
{
    protected $packageConfig;

    abstract public function configurePackage(Package $package): void;

    public function register()
    {
        $this->registeringPackage();

        $this->package = new Package();



        $this->package->setBasePath($this->getPackageBaseDir());

        //dd($this->package);

        $this->configurePackage($this->package);

        $this->app->bind(ucfirst($this->package->name), function($app) {
             $name=$this->package->namespace.ucfirst($this->package->name);
             return new $name();
        });

        if (empty($this->package->name)) {
            throw InvalidPackage::nameIsRequired();
        }

        if ($configFileName = $this->package->configFileName) {
            $this->mergeConfigFrom($this->package->basePath("/../config/{$configFileName}.php"), $configFileName);
        }

        $this->packageRegistered();

        return $this;
    }

    public function boot()
    {
        $this->bootingPackage();

        if ($this->app->runningInConsole()) {
            if ($configFileName = $this->package->configFileName) {
                $this->publishes([
                    $this->package->basePath("/../config/{$configFileName}.php") => config_path("{$configFileName}.php"),
                ], "{$this->package->name}-config");
            }

            if ($this->package->hasViews) {
                $this->publishes([
                    $this->package->basePath('/../resources/views') => base_path("resources/views/vendor/{$this->package->name}"),
                ], "{$this->package->name}-views");
            }

            foreach ($this->package->migrationFileNames as $migrationFileName) {
                if (! $this->migrationFileExists($migrationFileName)) {
                    $this->publishes([
                        $this->package->basePath("/../database/migrations/{$migrationFileName}.php.stub") => database_path('migrations/' . now()->format('Y_m_d_His') . '_' . Str::finish($migrationFileName, '.php')),
                    ], "{$this->package->name}-migrations");
                }
            }

            if ($this->package->hasTranslations) {
                $this->publishes([
                    $this->package->basePath('/../resources/lang') => resource_path("lang/vendor/{$this->package->shortName()}"),
                ], "{$this->package->name}-translations");
        }

            if ($this->package->hasAssets) {
                $this->publishes([
                    $this->package->basePath('/../resources/dist') => public_path("vendor/{$this->package->shortName()}"),
                ], "{$this->package->name}-assets");
            }

            $this->commands($this->package->commands);
        }

        if ($this->package->hasTranslations) {
            $this->loadTranslationsFrom(
                $this->package->basePath('/../resources/lang/'),
                $this->package->shortName()
            );
        }

        if ($this->package->hasViews) {
            $this->loadViewsFrom($this->package->basePath('/../resources/views'), $this->package->shortName());
        }

        foreach ($this->package->routeFileNames as $routeFileName) {
            $this->loadRoutesFrom("{$this->package->basePath('/../routes/')}{$routeFileName}.php");
        }

        $this->packageBooted();

        return $this;
    }

    public static function migrationFileExists(string $migrationFileName): bool
    {
        $len = strlen($migrationFileName) + 4;

        foreach (glob(database_path("migrations/*.php")) as $filename) {
            if ((substr($filename, -$len) === $migrationFileName . '.php')) {
                return true;
            }
        }

        return false;
    }

    public function registeringPackage()
    {
    }

    public function packageRegistered()
    {
    }

    public function bootingPackage()
    {
    }

    public function packageBooted()
    {
    }

    protected function getPackageBaseDir(): string
    {
        $reflector = new ReflectionClass(get_class($this));

        return dirname($reflector->getFileName());
    }
}
