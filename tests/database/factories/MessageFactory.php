<?php

namespace TimothePearce\TimeSeries\Tests\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use TimothePearce\TimeSeries\Tests\Models\Message;

class MessageFactory extends Factory
{
    protected $model = Message::class;

    public function definition()
    {
        return [
            'message' => $this->faker->text(),
        ];
    }
}
