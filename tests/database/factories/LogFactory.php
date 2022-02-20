<?php

namespace TimothePearce\TimeSeries\Tests\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use TimothePearce\TimeSeries\Tests\Models\Log;

class LogFactory extends Factory
{
    protected $model = Log::class;

    public function definition()
    {
        return [
            'message' => $this->faker->text(),
        ];
    }
}
