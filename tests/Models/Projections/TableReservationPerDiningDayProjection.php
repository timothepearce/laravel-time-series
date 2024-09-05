<?php

namespace TimothePearce\TimeSeries\Tests\Models\Projections;

use Illuminate\Database\Eloquent\Model;
use TimothePearce\TimeSeries\Contracts\ProjectionContract;
use TimothePearce\TimeSeries\Models\Projection;

class TableReservationPerDiningDayProjection extends Projection implements ProjectionContract
{
    /**
     * Lists the time intervals used to compute the projections.
     */
    public array $periods = ['1 day'];

    public string $dateColumn = 'reservation_date';

    /**
     * The default projection content.
     */
    public function defaultContent(): array
    {
        return [
            'total_people' => 0,
            'number_reservations' => 0,
        ];
    }

    /**
     * Computes the content when a projectable model is created.
     */
    public function projectableCreated(array $content, Model $model): array
    {
        return [
            'total_people' => $content['total_people'] += $model->number_people,
            'number_reservations' => $content['number_reservations'] + 1,
        ];
    }

    /**
     * Computes the content when a projectable model is deleted.
     */
    public function projectableDeleted(array $content, Model $model): array
    {
        return [
            'total_people' => $content['total_people'] -= $model->number_people,
            'number_reservations' => $content['number_reservations'] - 1,
        ];
    }
}
