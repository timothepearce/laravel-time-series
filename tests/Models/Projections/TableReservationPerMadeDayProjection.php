<?php

namespace TimothePearce\TimeSeries\Tests\Models\Projections;

class TableReservationPerMadeDayProjection extends TableReservationPerDiningDayProjection
{
    public function __construct()
    {
        $this->dateColumn = 'reservation_made_date';
    }
}
