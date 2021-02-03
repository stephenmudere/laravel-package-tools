<?php


namespace Stephenmudere\LaravelPackageTools;

use Illuminate\Support\ServiceProvider;
use Stephenmudere\LaravelPackageTools\Exceptions\InvalidPackage;

abstract class PackageServiceProvider extends ServiceProvider
{
    protected Package $packageConfig;

    abstract public function configurePackage(Package $package): void;

    public function register()
    {
        $this->registeringPackage();

        $this->packageConfig = new Package();

        $this->configurePackage($this->packageConfig);


        if (empty($this->packageConfig->name)) {
            throw InvalidPackage::nameIsRequired();
        }

        if ($configFileName = $this->packageConfig->configFileName) {
            $this->mergeConfigFrom(__DIR__ . "/../config/{$configFileName}.php", $configFileName);
        }

        $this->packageRegistered();
    }

    public function boot()
    {
        $this->bootingPackage();

        if ($this->app->runningInConsole()) {
            if ($configFileName = $this->packageConfig->configFileName) {
                $this->publishes([
                    __DIR__ . "/../config/{$configFileName}.php" => config_path("{$configFileName}.php"),
                ], "{$this->packageConfig->name}-config");
            }

            if ($this->packageConfig->hasViews) {
                $this->publishes([
                    __DIR__ . '/../resources/views' => base_path("resources/views/vendor/{$this->packageConfig->name}"),
                ], "{$this->packageConfig->name}-views");
            }

            foreach ($this->packageConfig->migrationFileNames as $migrationFileName) {
                if (! $this->migrationFileExists($migrationFileName)) {
                    $this->publishes([
                        __DIR__ . "/../database/migrations/{$migrationFileName}.php.stub" => database_path('migrations/' . now()->format('Y_m_d_His') . '_' . $migrationFileName),
                    ], "{$this->packageConfig->name}-migrations");
                }
            }

            $this->commands($this->packageConfig->commands);
        }

        if ($this->packageConfig->hasViews) {
            $this->loadViewsFrom(__DIR__ . '/../resources/views', $this->packageConfig->name);
        }

        $this->packageBooted();
    }

    public static function migrationFileExists(string $migrationFileName): bool
    {
        $len = strlen($migrationFileName);

        foreach (glob(database_path("migrations/*.php")) as $filename) {
            if ((substr($filename, -$len) === $migrationFileName)) {
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
}
