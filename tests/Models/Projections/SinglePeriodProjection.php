<?php

namespace TimothePearce\Quasar\Tests\Models\Projections;

use Illuminate\Database\Eloquent\Model;
use TimothePearce\Quasar\Contracts\ProjectionContract;
use TimothePearce\Quasar\Models\Projection;

class SinglePeriodProjection extends Projection implements ProjectionContract
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
            'created_count' => 0,
            'updated_count' => 0,
        ];
    }

    /**
     * Computes the content when a projectable model is created.
     */
    public static function projectableCreated(array $content, Model $model): array
    {
        return [
            'created_count' => $content['created_count'] + 1,
        ];
    }

    /**
     * Computes the content when a projectable model is updated.
     */
    public static function projectableUpdated(array $content, Model $model): array
    {
        return [
            'updated_count' => $content['updated_count'] + 1,
        ];
    }
}
