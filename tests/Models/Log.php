<?php

namespace Laravelcargo\LaravelCargo\Tests\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Laravelcargo\LaravelCargo\WithProjections;

class Log extends Model
{
    use HasFactory;
    use WithProjections;

    /**
     * Lists the time intervals used to compute the projections.
     *
     * @var string[]
     */
    protected $intervals = [
        '5 minutes',
        '1 hour',
        '6 hours',
        '1 day',
        '1 week',
        '1 month',
        '3 months',
        '1 year',
        '*',
    ];

    /**
     * The default projection content.
     *
     * @return array
     */
    public function defaultProjection()
    {
        return [
            'total words' => 0,
            'number of log' => 0,
        ];
    }

    /**
     * Compute the projection.
     *
     * @return array
     */
    public function project(CargoProjection $projection, Collection $previousLogs)
    {
        return [
            'total words' => $projection['average message words'] + Str::length($this->message),
            'number of logs' => $previousLogs->count() + 1,
        ];
    }

    /**
     * Each time a model is created, we dispatch an event for each interval defined.
     *
     * The time interval is used to query all the models between now and (now - time interval).
     *
     * Then, we call the projection method by passing it :
     * - The CargoProjection model (a default projection will be provided if no one exists)
     * - The collection of the previously queried models
     *
     * The projection method will return an array which will be stored as the `content` attribute of the CargoProjection model.
     */
    public function relationWithCargoProjection()
    {
        $projections = $this->projections()->get(); // Get all the projections

        $this->projectionsFromInterval('5 minutes'); // Get all the projections for the given interval
        $this->lastProjectionFromInterval('1 hour'); // Get the latest projection for the given interval
        $this->firstProjectionFromInterval('1 day'); // Get the first projection for the given interval
        $this->projectionsByIntervals(); // Get all the projections ordered by intervals

        $this->projections; // Give a super set of the default collection instance with useful methods
    }
}
