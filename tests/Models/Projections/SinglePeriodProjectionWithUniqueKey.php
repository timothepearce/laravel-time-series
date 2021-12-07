<?php

namespace TimothePearce\Quasar\Tests\Models\Projections;

use Illuminate\Database\Eloquent\Model;
use TimothePearce\Quasar\Contracts\ProjectionContract;
use TimothePearce\Quasar\Models\Projection;

class SinglePeriodProjectionWithUniqueKey extends Projection implements ProjectionContract
{
    /**
     * Lists the time intervals used to compute the projections.
     */
    public array $periods = ['5 minutes'];

    /**
     * The default projection content.
     */
    public function defaultContent(): array
    {
        return [
            'created_count' => 0,
        ];
    }

    /**
     * The key used to query the projection.
     */
    public static function key(Model $model): string
    {
        return (string)$model->id;
    }

    /**
     * Compute the projection.
     */
    public function projectableCreated(array $content, Model $model): array
    {
        return [
            'created_count' => $content['created_count'] + 1,
        ];
    }
}
