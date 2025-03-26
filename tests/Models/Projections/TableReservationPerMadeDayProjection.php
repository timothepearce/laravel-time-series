<?php

namespace TimothePearce\TimeSeries\Tests\Models\Projections;

use Illuminate\Database\Eloquent\Model;
use TimothePearce\TimeSeries\Contracts\ProjectionContract;
use TimothePearce\TimeSeries\Models\Projection;

class TableReservationPerMadeDayProjection extends TableReservationPerDiningDayProjection
{
    public function __construct() {
        $this->dateColumn = 'reservation_made_date';
    }
}
