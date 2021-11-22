<?php

namespace Laravelcargo\LaravelCargo;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Laravelcargo\LaravelCargo\Jobs\ProcessProjection;
use Laravelcargo\LaravelCargo\Models\Projection;
use ReflectionException;

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
     * @throws ReflectionException
     */
    public function bootProjectors(): void
    {
        collect($this->projections)->each(
            fn (string $projection) =>
            (new Projector($this, $projection))->parsePeriods()
        );
    }

    /**
     * Get all the projections of the model.
     */
    public function projections(
        string | null $projectorName = null,
        string | array | null $periods = null,
    ): MorphToMany {
        $query = $this->morphToMany(Projection::class, 'projectable', 'cargo_projectables');

        if (isset($projectorName)) {
            $query->where('projector_name', $projectorName);
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
     * Get the first projection.
     */
    public function firstProjection(
        string | null $projectorName = null,
        string | array | null $periods = null,
    ): null|Projection {
        return $this->projections($projectorName, $periods)->first();
    }

    /**
     * Set the projectors.
     */
    public function setProjections(array $projections)
    {
        $this->projections = $projections;
    }
}
