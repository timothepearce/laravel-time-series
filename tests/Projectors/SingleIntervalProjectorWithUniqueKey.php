<?php

namespace Laravelcargo\LaravelCargo\Tests\Projectors;

use Illuminate\Database\Eloquent\Model;
use Laravelcargo\LaravelCargo\Projector;

class SingleIntervalProjectorWithUniqueKey extends Projector
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
            'number of logs' => 0,
        ];
    }

    /**
     * The key used to query the projection.
     */
    public function key(Model $model): string
    {
        return (string) $model->id;
    }

    /**
     * Compute the projection.
     */
    public function handle(array $content, Model $model): array
    {
        return [
            'number of logs' => $content['number of logs'] + 1,
        ];
    }
}
