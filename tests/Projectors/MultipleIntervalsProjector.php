<?php

namespace Laravelcargo\LaravelCargo\Tests\Projectors;

use Laravelcargo\LaravelCargo\Models\Projection;
use Laravelcargo\LaravelCargo\Projector;

class MultipleIntervalsProjector extends Projector
{
    /**
     * Lists the time intervals used to compute the projections.
     *
     * @var string[]
     */
    protected array $periods = [
        '5 minutes',
        '1 hour',
        '6 hours',
        '1 day',
        '1 week',
        '1 month',
        '3 months',
        '1 year',
    ];

    /**
     * The default projection content.
     */
    public function defaultContent(): array
    {
        return [
            'total words' => 0,
            'number of logs' => 0,
        ];
    }

    /**
     * Compute the projection's content.
     */
    public function handle(array $content): array
    {
        return [
            'total words' => $content['total words'],
            'number of logs' => $content['number of logs'] + 1,
        ];
    }
}
