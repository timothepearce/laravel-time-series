<?php

namespace TimothePearce\TimeSeries\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use TimothePearce\TimeSeries\Models\Projection;

class ProjectionScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        if (! $this->isAbstractProjection($model)) {
            $builder->whereRaw('projection_name = ?', [$model::class]);
        }
    }

    /**
     * Determines either the given model is the abstract projection one or not.
     */
    private function isAbstractProjection(Model $model): bool
    {
        return $model::class === Projection::class;
    }
}
