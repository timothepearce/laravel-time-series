<?php

namespace Laravelcargo\LaravelCargo\Tests\Projectors;

use Illuminate\Database\Eloquent\Model;
use Laravelcargo\LaravelCargo\Contracts\ProjectionContract;
use Laravelcargo\LaravelCargo\Models\Projection;

class SinglePeriodProjectorWithUniqueKey extends Projection implements ProjectionContract
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
     * The key used to query the projection.
     */
    public function key(Model $model): string
    {
        return (string) $model->id;
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
