<?php

namespace Laravelcargo\LaravelCargo\Tests\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Laravelcargo\LaravelCargo\Tests\Models\A;

class AFactory extends Factory
{
    protected $model = A::class;

    public function definition()
    {
        return [
            'message' => $this->faker->text(),
        ];
    }
}
