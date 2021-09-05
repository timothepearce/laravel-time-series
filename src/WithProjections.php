<?php

namespace Laravelcargo\LaravelCargo;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Laravelcargo\LaravelCargo\Jobs\ProcessProjection;
use Laravelcargo\LaravelCargo\Models\Projection;

trait WithProjections
{
    /**
     * Boot the trait.
     */
    public static function bootWithProjections(): void
    {
        static::created(function (Model $model) {
            config('cargo.queue') ?
                ProcessProjection::dispatch($model) :
                $model->bootProjectors();
        });
    }

    /**
     * Boot the projectors.
     */
    public function bootProjectors(): void
    {
        collect($this->projectors)->each(
            fn (string $projector) =>
            (new $projector($this))->parsePeriods()
        );
    }

    /**
     * Get all the projections of the model.
     */
    public function projections(string | null $projectionName = null, string | array | null $periods = null): MorphToMany
    {
        $query = $this->morphToMany(Projection::class, 'projectable', 'cargo_projectables');

        if (isset($projectionName)) {
            $query->where('name', $projectionName);
        }

        if (isset($periods) && gettype($periods) === 'string') {
            $query->where('period', $periods);
        } elseif (isset($periods) && gettype($periods) === 'array') {
            $query->where(function ($query) use (&$periods) {
                collect($periods)->each(function (string $period, $key) use (&$query) {
                    $key === 0 ?
                        $query->where('period', $period) :
                        $query->orWhere('period', $period);
                });
            });
        }

        return $query;
    }

    /**
     * Set the projectors.
     */
    public function setProjectors(array $projectors)
    {
        $this->projectors = $projectors;
    }
}
