<?php

namespace Laravelcargo\LaravelCargo\Tests;

use Illuminate\Database\Eloquent\Model;

trait WithProjectableFactory
{
    /**
     * Create the model with the given projectors.
     */
    public function createModelWithProjectors(string $modelName, array $projectors): Model
    {
        $model = $modelName::factory()->make();

        $model->setProjectors($projectors);
        $model->save();

        return $model;
    }
}
