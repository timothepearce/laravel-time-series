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
    public function projections(string|null $projectionName = null): MorphToMany
    {
        $relation = $this->morphToMany(Projection::class, 'projectable', 'cargo_projectables');

        if (isset($projectionName)) {
            $relation->where('projection_name', $projectionName);
        }

        return $relation;
    }

    /**
     * Set the projectors.
     */
    public function setProjectors(array $projectors)
    {
        $this->projectors = $projectors;
    }
}
