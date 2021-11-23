<?php

namespace TimothePearce\Quasar\Tests;

use Illuminate\Database\Eloquent\Model;

trait WithProjectableFactory
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
