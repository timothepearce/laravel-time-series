<?php

namespace Laravelcargo\LaravelCargo\Tests\Projectors;

use Illuminate\Database\Eloquent\Model;
use Laravelcargo\LaravelCargo\Contracts\ProjectionContract;
use Laravelcargo\LaravelCargo\Models\Projection;

class SinglePeriodProjector extends Projection implements ProjectionContract
{
    /**
     * Lists the time intervals used to compute the projections.
     */
    public static array $periods = ['5 minutes'];

    /**
     * The default projection content.
     */
    public static function defaultContent(): array
    {
        return [
            'number of logs' => 0,
        ];
    }

    /**
     * Compute the projection.
     */
    public static function handle(array $content, Model $model): array
    {
        return [
            'number of logs' => $content['number of logs'] + 1,
        ];
    }
}
