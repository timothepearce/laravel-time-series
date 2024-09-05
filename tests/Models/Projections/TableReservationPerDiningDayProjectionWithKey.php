<?php

namespace TimothePearce\TimeSeries\Tests\Models\Projections;

use Illuminate\Database\Eloquent\Model;
use TimothePearce\TimeSeries\Contracts\ProjectionContract;
use TimothePearce\TimeSeries\Models\Projection;

class TableReservationPerDiningDayProjectionWithKey extends TableReservationPerDiningDayProjection
{
    /**
     * The key used to query the projection.
     */
    public static function key(Model $model): string
    {
        return (string)$model->table_id;
    }
}
