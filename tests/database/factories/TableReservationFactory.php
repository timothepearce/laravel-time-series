<?php

namespace TimothePearce\TimeSeries\Tests\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use TimothePearce\TimeSeries\Tests\Models\TableReservation;

class TableReservationFactory extends Factory
{
    protected $model = TableReservation::class;

    public function definition()
    {
        return [
            'table_id'  => 1, //$this->random_int(1, 10),
            'customer_name' => $this->faker->name,
            'reservation_date' => today()->addDays(10)->format('Y-m-d'),
            'reservation_made_date' => today()->format('Y-m-d H:00'),
            'number_people' => 2, //$this->random_int(1, 10),
        ];
    }
}


