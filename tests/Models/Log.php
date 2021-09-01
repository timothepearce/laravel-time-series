<?php

namespace Laravelcargo\LaravelCargo\Tests\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Laravelcargo\LaravelCargo\Models\Projection;
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
    protected array $intervals = [
        '5 minutes',
        '1 hour',
        '6 hours',
        '1 day',
        '1 week',
        '1 month',
        '3 months',
        '1 year',
        // '*'
    ];

    /**
     * The default projection content.
     */
    public function defaultProjection(): array
    {
        return [
            'total words' => 0,
            'number of logs' => 0,
        ];
    }

    /**
     * Compute the projection.
     */
    public function project(Projection $projection): array
    {
        return [
            'total words' => $projection->content['total words'],
            'number of logs' => $projection->content['number of logs'] + 1,
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
