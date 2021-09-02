<?php

namespace Laravelcargo\LaravelCargo\Tests\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Laravelcargo\LaravelCargo\Tests\Models\B;

class BFactory extends Factory
{
    protected $model = B::class;

    public function definition()
    {
        return [
            'message' => $this->faker->text(),
        ];
    }
}
