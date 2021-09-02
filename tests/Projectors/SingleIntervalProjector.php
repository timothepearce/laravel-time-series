<?php

namespace Laravelcargo\LaravelCargo\Tests\Projectors;

use Laravelcargo\LaravelCargo\Models\Projection;
use Laravelcargo\LaravelCargo\Projector;

class SingleIntervalProjector extends Projector
{
    /**
     * Lists the time intervals used to compute the projections.
     *
     * @var string[]
     */
    protected array $intervals = ['5 minutes'];

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
     * Compute the projection.
     */
    public function handle(Projection $projection): array
    {
        return [
            'total words' => $projection->content['total words'],
            'number of logs' => $projection->content['number of logs'] + 1,
        ];
    }
}
