<?php

namespace TimothePearce\TimeSeries\Tests;

use Illuminate\Database\Eloquent\Model;

trait ProjectableFactory
{
    /**
     * Create the model with the given projectors.
     */
    public function createModelWithProjections(string $modelName, array $projections): Model
    {
        $model = $modelName::factory()->make();

        $model->setProjections($projections);
        $model->save();

        return $model;
    }
}
