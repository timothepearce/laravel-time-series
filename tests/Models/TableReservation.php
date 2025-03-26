<?php

namespace TimothePearce\TimeSeries\Tests\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use TimothePearce\TimeSeries\Models\Traits\Projectable;
use TimothePearce\TimeSeries\Tests\Models\Projections\TableReservationPerDiningDayProjection;
use TimothePearce\TimeSeries\Tests\Models\Projections\TableReservationPerMadeDayProjection;

class TableReservation extends Model
{
    use HasFactory;
    use Projectable;
    use SoftDeletes;

    protected $casts = [
        'reservation_date' => 'datetime:Y-m-d',
        'reservation_made_date' => 'datetime:Y-m-d H:00',
    ];
    /**
     * The projections list.
     */
    protected array $projections = [
        TableReservationPerMadeDayProjection::class,
        TableReservationPerDiningDayProjection::class,
    ];
}
