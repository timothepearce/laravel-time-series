<?php

namespace Laravelcargo\LaravelCargo\Tests\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Laravelcargo\LaravelCargo\Tests\Models\Log;

class MessageFactory extends Factory
{
    protected $model = Log::class;

    public function definition()
    {
        return [
            'message' => $this->faker->text(),
        ];
    }
}
