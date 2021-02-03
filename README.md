# Tools for creating Laravel packages

[![Latest Version on Packagist](https://img.shields.io/packagist/v/spatie/laravel-package-tools.svg?style=flat-square)](https://packagist.org/packages/spatie/laravel-package-tools)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/spatie/laravel-package-tools/run-tests?label=tests)](https://github.com/spatie/laravel-package-tools/actions?query=workflow%3ATests+branch%3Amaster)
[![Total Downloads](https://img.shields.io/packagist/dt/spatie/laravel-package-tools.svg?style=flat-square)](https://packagist.org/packages/spatie/laravel-package-tools)

This package contains a `PackageServiceProvider` that you can use in your packages to easily register config files,
migrations, and more.

Here's an example of how it can be used.

```php
use Stephenmudere\LaravelPackageTools\PackageServiceProvider;
use Stephenmudere\LaravelPackageTools\Package;

class YourPackageServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package) : void
    {
        $package
            ->name('your-package-name')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_package_tables')
            ->hasCommand(YourCoolPackageCommand::class);
    }
}
```

Under the hood it will do the necessary work to register the necessary things and make all sorts of files publishable.

## Support us

[<img src="https://github-ads.s3.eu-central-1.amazonaws.com/package-laravel-package-tools-laravel.jpg?t=1" width="419px" />](https://spatie.be/github-ad-click/package-laravel-package-tools-laravel)

We invest a lot of resources into creating [best in class open source packages](https://spatie.be/open-source). You can
support us by [buying one of our paid products](https://spatie.be/open-source/support-us).

We highly appreciate you sending us a postcard from your hometown, mentioning which of our package(s) you are using.
You'll find our address on [our contact page](https://spatie.be/about-us). We publish all received postcards
on [our virtual postcard wall](https://spatie.be/open-source/postcards).

## Getting started

This package is opinionated on how you should structure your package. To get started easily, consider using [our package-skeleton repo](https://github.com/spatie/package-skeleton-laravel) to start your package. The skeleton is structured perfectly to work perfectly with the `PackageServiceProvider` in this package.

## Usage

In your package you should let your service provider extend `Stephenmudere\LaravelPackageTools\PackageServiceProvider`.

```php
use Stephenmudere\LaravelPackageTools\PackageServiceProvider;
use Stephenmudere\LaravelPackageTools\Package;

class YourPackageServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package) : void
    {
        $package->name('your-package-name');
    }
}
```

Passing the package name to `name` is mandatory.

### Working with a config file

To register a config file, you should create a php file with your package name in the `config` directory of your package. In this example it should be at `<package root>/config/your-package-name.php`.

To register that config file, call `hasConfigFile()` on `$package` in the `configurePackage` method.

```php
$package
    ->name('your-package-name')
    ->hasConfigFile();
```

This will register your config file with Laravel. It will also make it publishable. Users of your package will be able to publish the config file with this command.

```bash
php artisan vendor:publish --tag=your-package-name-config
```

### Working with views

Any views your package provides, should be place in the `<package root>/resources/views` directory.

You can register these views with the `hasViews` command.

```php
$package
    ->name('your-package-name')
    ->hasViews();
```

This will register your views with Laravel.

If you have a view `<package root>/resources/views/myView.blade.php`, you can use it like this: `view('your-package-name::myView')`. Of course, you can also use subdirectories to organise your views. A view located at `<package root>/resources/views/subdirectory/myOtherView.blade.php` can be used with `view('your-package-name::myOtherView')`.


Calling `hasViews` will also make views publishable. Users of your package will be able to publish the config file with this command:

```bash
php artisan vendor:publish --tag=your-package-name-views
```

### Working with migrations

The `PackageServiceProvider` assumes that any migrations are placed in this directory: `<package root>/database/migration`. Inside that directory you can put any migrations. Make sure they all have a `php.stub` extension. Using that extension will make sure that static analysers won't get confused with classes existing in multiple when your migration gets published.

To register your migration, you should pass its name without the extension to the `hasMigration` table. 

If your migration file is called `create_my_package_tables.php.stub` you can register them like this:

```php
$package
    ->name('your-package-name')
    ->hasMigration('my_package_tables');
```

Should your package contain multiple migration files, you can just call `hasMigration` multiple times.

Calling `hasViews` will also make migrations publishable. Users of your package will be able to publish the config file with this command:

```bash
php artisan vendor:publish --tag=your-package-name-migrations
```

Like you might expect, published migration files will be prefixed with the current datetime.

### Registering commands

You can register any command you package provides with the `hasCommand` function.

```php
$package
    ->name('your-package-name')
    ->hasCommand(YourCoolPackageCommand::class);
````

If your package provides multiple commands, you can either use `hasCommand` multiple times, or pass an array to `hasCommands`

```php
$package
    ->name('your-package-name')
    ->hasCommands([
        YourCoolPackageCommand::class,
        YourOtherCoolPackageCommand::class,
    ]);
```

### Using lifecycle hooks

You can put any custom logic your package needs while starting up in one of these methods:

- `registeringPackage`: will be called at the start of the `register` method of `PackageServiceProvider` 
- `packageRegistered`: will be called at the end of the `register` method of `PackageServiceProvider`
- `bootingPackage`: will be called at the start of the `boot` method of `PackageServiceProvider`
- `packageBooted`: will be called at the end of the `boot` method of `PackageServiceProvider`

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Freek Van der Herten](https://github.com/freekmurze)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
