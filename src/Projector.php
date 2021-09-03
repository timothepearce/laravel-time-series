<?php

namespace Laravelcargo\LaravelCargo;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Laravelcargo\LaravelCargo\Models\Projection;

abstract class Projector
{
    protected Model $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * Parse the intervals defined as class attribute.
     */
    public function parseIntervals(): void
    {
        collect($this->intervals)->each(fn ($interval) => $this->parseInterval($interval));
    }

    /**
     * Parse the given interval and return the projection.
     */
    private function parseInterval(string $interval): Projection
    {
        [$unit, $period] = Str::of($interval)->split('/[\s]+/');

        return $this->findOrCreateProjection($interval, $unit, $period);
    }

    /**
     * Find or create the projection.
     */
    private function findOrCreateProjection(string $interval, string $unit, string $period): Projection
    {
        $projection = Projection::firstOrNew([
            'model_name' => $this->model::class,
            'interval_name' => $interval,
            'interval_start' => Carbon::now()->floorUnit($period, (int) $unit),
            'interval_end' => Carbon::now()->floorUnit($period, (int) $unit)->add((int) $unit, $period),
        ], ['content' => $this->defaultContent()]);

        $projection->content = $this->handle($projection);

        $projection->save();
        $this->model->projections()->attach($projection);

        return $projection;
    }

    /**
     * Set the intervals.
     */
    public function setInterval(array $newIntervals): void
    {
        $this->intervals = $newIntervals;
    }
}
