<?php

namespace TimothePearce\TimeSeries\Tests\Models\Projections;

use Illuminate\Database\Eloquent\Model;
use TimothePearce\TimeSeries\Contracts\ProjectionContract;
use TimothePearce\TimeSeries\Models\Projection;

class GlobalPeriodProjection extends Projection implements ProjectionContract
{
    /**
     * Lists the time intervals used to compute the projections.
     */
    public array $periods = ['*'];

    /**
     * The default projection content.
     */
    public function defaultContent(): array
    {
        return [
            'created_count' => 0,
            'updated_count' => 0,
        ];
    }

    /**
     * Computes the content when a projectable model is created.
     */
    public function projectableCreated(array $content, Model $model): array
    {
        return [
            'created_count' => $content['created_count'] + 1,
        ];
    }

    /**
     * Computes the content when a projectable model is updated.
     */
    public function projectableUpdated(array $content, Model $model): array
    {
        return [
            'updated_count' => $content['updated_count'] + 1,
        ];
    }
}
