<?php

namespace TimothePearce\TimeSeries\Models\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use TimothePearce\TimeSeries\Jobs\ComputeProjection;
use TimothePearce\TimeSeries\Models\Projection;
use TimothePearce\TimeSeries\Projector;

trait Projectable
{
    /**
     * Boots the trait.
     */
    public static function bootProjectable(): void
    {
        static::created(fn (Model $model) => $model->projectModel('created'));
        static::updating(fn (Model $model) => $model->projectModel('updating'));
        static::updated(fn (Model $model) => $model->projectModel('updated'));
        static::deleting(fn (Model $model) => $model->projectModel('deleting'));
        static::deleted(fn (Model $model) => $model->projectModel('deleted'));
    }

    /**
     * Projects the model.
     */
    public function projectModel(string $eventName): void
    {
        config('time-series.queue') ?
            ComputeProjection::dispatch($this, $eventName) :
            $this->bootProjectors($eventName);
    }

    /**
     * Boots the projectors.
     */
    public function bootProjectors(string $eventName): void
    {
        collect($this->projections)->each(
            fn (string $projection) => (new Projector($this, $projection, $eventName))->handle()
        );
    }

    /**
     * Gets all the projections of the model.
     */
    public function projections(
        string|null       $projectionName = null,
        string|array|null $periods = null,
    ): MorphToMany {
        $query = $this->morphToMany(Projection::class, 'projectable', 'time_series_projectables');

        if (isset($projectionName)) {
            $query->whereRaw('projection_name = ?', [$projectionName]);
        }

        if (isset($periods) && is_string($periods)) {
            $query->where('period', $periods);
        } elseif (isset($periods) && is_array($periods)) {
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
     * Gets the first projection.
     */
    public function firstProjection(
        string|null       $projectionName = null,
        string|array|null $periods = null,
    ): null|Projection {
        return $this->projections($projectionName, $periods)->first();
    }

    /**
     * Sets the projectors.
     */
    public function setProjections(array $projections)
    {
        $this->projections = $projections;
    }
}
