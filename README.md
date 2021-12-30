# Laravel Quasar

<h1 align="center">
    <img src="https://timothepearce.github.io/laravel-quasar-docs/img/logo.svg" width="32px" height="32px" /> Laravel Quasar
</h1>

    [![Latest Version on Packagist](https://img.shields.io/packagist/v/timothepearce/laravel-quasar.svg?style=flat-square)](https://packagist.org/packages/timothepearce/laravel-quasar)
    [![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/timothepearce/laravel-quasar/run-tests?label=tests)](https://github.com/timothepearce/laravel-quasar/actions?query=workflow%3Arun-tests+branch%3Amain)
    [![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/timothepearce/laravel-quasar/Check%20&%20fix%20styling?label=code%20style)](https://github.com/timothepearce/laravel-quasar/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amain)
    [![Total Downloads](https://img.shields.io/packagist/dt/timothepearce/laravel-quasar.svg?style=flat-square)](https://packagist.org/packages/timothepearce/laravel-quasar)

<p align="center">Laravel Quasar provides an API to create and maintain data projections (statistics, aggregates, etc.) from you Eloquent models.</p>

## Installation

You can install the package via composer:

```bash
composer require timothepearce/laravel-quasar
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --provider="TimothePearce\Quasar\QuasarServiceProvider" --tag="quasar-migrations"
php artisan migrate
```

You can publish the config file with:
```bash
php artisan vendor:publish --provider="TimothePearce\Quasar\QuasarServiceProvider" --tag="quasar-config"
```

This is the contents of the published config file:

## Usage

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
