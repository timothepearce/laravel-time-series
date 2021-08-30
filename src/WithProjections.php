<?php

namespace Laravelcargo\LaravelCargo;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Laravelcargo\LaravelCargo\Models\Projection;

trait WithProjections
{
    /**
     * Boot the trait.
     *
     * @return void
     */
    public static function bootWithProjections()
    {
        static::created(function (Model $model) {
            $model->parseIntervals();
        });
    }

    /**
     * Parse the intervals defined as class attribute.
     *
     * @return void
     */
    private function parseIntervals()
    {
        collect($this->intervals)->each(fn ($interval) => $this->parseInterval($interval));
    }

    /**
     * Parse the given interval and return the projection.
     *
     * @param string $interval
     *
     * @return Projection
     */
    private function parseInterval(string $interval)
    {
        [$unit, $period] = Str::of($interval)->split($interval, ' ');

        return $this->findOrCreateProjection($interval, $unit, $period);
    }

    /**
     * Find or create the projection.
     *
     * @param string $interval
     * @param string $unit
     * @param int $period
     *
     * @return Collection
     */
    private function findOrCreateProjection(string $interval, string $unit, int $period)
    {
        // Find the end date (round to something ?)
        // Find the start date
        // Query the model filtered by the period name and between the dates computed

        // Format to UTC?
        $endDate = Carbon::now()->floorUnit($unit, $period)->format('H:i:s.u');

        // Call the right unit function
        $startDate = $endDate->minMinutes($period);

        return Projection::between($startDate, $endDate)
            ->where('interval', $interval)
            ->findOrCreate($this->defaultProjection());
    }
}
