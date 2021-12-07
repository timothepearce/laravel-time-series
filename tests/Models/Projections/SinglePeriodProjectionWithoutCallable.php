<?php

namespace TimothePearce\Quasar\Tests\Models\Projections;

use TimothePearce\Quasar\Contracts\ProjectionContract;
use TimothePearce\Quasar\Models\Projection;

class SinglePeriodProjectionWithoutCallable extends Projection implements ProjectionContract
{
    /**
     * Lists the time intervals used to compute the projections.
     */
    public static array $periods = ['5 minutes'];

    /**
     * The default projection content.
     */
    public function defaultContent(): array
    {
        return [
            'created_count' => 0,
            'updated_count' => 0,
            'deleted_count' => 0,
        ];
    }
}
