![Logo](./static/logo.svg#gh-light-mode-only)![Logo](./static/logo_white.svg#gh-dark-mode-only)

<p align="center">
<a href="https://packagist.org/packages/timothepearce/laravel-time-series">
    <img src="http://poser.pugx.org/timothepearce/laravel-time-series/v/unstable" alt="Latest unstable Version" />
</a>
<a href="https://packagist.org/packages/timothepearce/laravel-time-series">
    <img src="http://poser.pugx.org/timothepearce/laravel-time-series/downloads" alt="Download count" />
</a>
<a href="https://github.com/timothepearce/laravel-time-series/actions/workflows/run-tests.yml">
    <img src="https://github.com/timothepearce/laravel-time-series/actions/workflows/run-tests.yml/badge.svg" alt="">
</a>
</p>

<p align="center">
Build your time series with ease
</p>

## About

Laravel Time Series provides an API to projects data from your Eloquent models, and convert them to time series.

## Documentation

The full documentation can be found [here](https://timothepearce.github.io/laravel-time-series-docs).

## Usage

### Installation

```bash
composer require timothepearce/laravel-time-series
```

### Migrate the tables

```bash
php artisan migrate
```

### Create a Projection

```bash
php artisan make:projection MyProjection
```

### Make a model projectable

When you want to make your model projectable, you must add it the `Projectable` trait and define the `$projections` class attribute:

```php
use App\Models\Projections\MyProjection;
use TimothePearce\TimeSeries\Projectable;

class MyProjectableModel extends Model
{
    use Projectable;

    protected array $projections = [
        MyProjection::class,
    ];
}
```

### Implement a Projection

When you're implementing a projection, follow theses three steps:
* [Define your projection periods](https://timothepearce.github.io/laravel-time-series-docs/getting-started/implement-a-projection#define-your-projection-periods)
* [Add a default content](https://timothepearce.github.io/laravel-time-series-docs/getting-started/implement-a-projection#define-the-default-content-of-your-projection)
* [Bind your projection to the projectable models](https://timothepearce.github.io/laravel-time-series-docs/getting-started/implement-a-projection#implement-the-binding)

### Query a Projection

A Projection is an Eloquent model and is queried the same way, but keep in mind that the projections are all stored in a single table.
That means you'll have to use scope methods to get the correct projections regarding the period you defined earlier:

```php
MyProjection::period('1 day')
    ->between(
        today()->subDay(), // start date
        today(), // end date
    )
    ->get();
```

### Query a time series

To get a time series from a projection model, use the toTimeSeries method:

```php
MyProjection::period('1 day')
    ->toTimeSeries(
        today()->subDay(),
        today(),
    );
```

Note that this method **fill the missing projections between the given dates** with the default content you defined earlier.

## Credits

- [Timothé Pearce](https://github.com/timothepearce)
- [All contributors](https://github.com/timothepearce/laravel-time-series/contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
