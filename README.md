# Laravel Cargo

[![Latest Version on Packagist](https://img.shields.io/packagist/v/laravelcargo/laravel-cargo.svg?style=flat-square)](https://packagist.org/packages/laravelcargo/laravel-cargo)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/laravelcargo/laravel-cargo/run-tests?label=tests)](https://github.com/laravelcargo/laravel-cargo/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/laravelcargo/laravel-cargo/Check%20&%20fix%20styling?label=code%20style)](https://github.com/laravelcargo/laravel-cargo/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/laravelcargo/laravel-cargo.svg?style=flat-square)](https://packagist.org/packages/laravelcargo/laravel-cargo)

This is where your description should go. Limit it to a paragraph or two. Consider adding a small example.

## Installation

You can install the package via composer:

```bash
composer require laravelcargo/laravel-cargo
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --provider="Laravelcargo\LaravelCargo\QuasarServiceProvider" --tag="laravel-cargo-migrations"
php artisan migrate
```

You can publish the config file with:
```bash
php artisan vendor:publish --provider="Laravelcargo\LaravelCargo\QuasarServiceProvider" --tag="laravel-cargo-config"
```

This is the contents of the published config file:

```php
return [
];
```

## Usage

```php
$laravelCargo = new Laravelcargo\LaravelCargo();
echo $laravelCargo->echoPhrase('Hello, Spatie!');
```

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

- [Timothé Pearce](https://github.com/TimothePearce)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
