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
        collect($this->projectors)->each(fn (string $projector) =>
            (new $projector($this))->parseIntervals()
        );
    }

    /**
     * Get all the projections of the model.
     */
    public function projections(): MorphToMany
    {
        return $this->morphToMany(Projection::class, 'projectable', 'cargo_projectables');
    }
}
