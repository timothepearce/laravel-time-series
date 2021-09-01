<?php

namespace Laravelcargo\LaravelCargo;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Laravelcargo\LaravelCargo\Models\Projection;

trait WithProjections
{
    /**
     * Boot the trait.
     */
    public static function bootWithProjections(): void
    {
        static::created(function (Model $model) {
            $model->parseIntervals();
        });
    }

    /**
     * Parse the intervals defined as class attribute.
     */
    private function parseIntervals(): void
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
            'model_name' => self::class,
            'interval_name' => $interval,
            'interval_start'=> Carbon::now()->floorUnit($period, (int) $unit),
            'interval_end' => Carbon::now()->floorUnit($period, (int) $unit)->add((int) $unit, $period),
        ]);

        if (is_null($projection->content)) {
            $projection->content = $this->defaultProjection();
        }

        $projection->content = $this->project($projection);
        $projection->save();

        return $projection;
    }

    /**
     * Get the interval count.
     */
    public function getIntervalCount(): int
    {
        return count($this->intervals);
    }

    /**
     * Set the intervals.
     */
    public function setInterval(array $newIntervals): void
    {
        $this->intervals = $newIntervals;
    }
}
