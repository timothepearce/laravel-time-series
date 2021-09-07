<?php

namespace Laravelcargo\LaravelCargo\Tests\Projectors;

use Illuminate\Database\Eloquent\Model;
use Laravelcargo\LaravelCargo\Projector;

class SingleIntervalProjector extends Projector
{
    /**
     * Lists the time intervals used to compute the projections.
     *
     * @var string[]
     */
    protected array $periods = ['5 minutes'];

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
    public function handle(array $content, Model $model): array
    {
        return [
            'total words' => $content['total words'],
            'number of logs' => $content['number of logs'] + 1,
        ];
    }
}
