<?php

namespace TimothePearce\Quasar\Tests\Models\Projections;

use Illuminate\Database\Eloquent\Model;
use TimothePearce\Quasar\Contracts\ProjectionContract;
use TimothePearce\Quasar\Models\Projection;

class SinglePeriodProjectionWithCallable extends Projection implements ProjectionContract
{
    /**
     * Lists the time intervals used to compute the projections.
     */
    public array $periods = ['5 minutes'];

    /**
     * The default projection content.
     */
    public function defaultContent(): array
    {
        return [
            'created_count' => 0,
            'updated_count' => 0,
            'deleted_count' => 0,
        ];
    }

    /**
     * Computes the content when a Log is created.
     */
    public static function logCreated(array $content, Model $model): array
    {
        return [
            'created_count' => $content['created_count'] + 1,
        ];
    }

    /**
     * Computes the content when a Log is updated.
     */
    public static function logUpdated(array $content, Model $model): array
    {
        return [
            'updated_count' => $content['updated_count'] + 1,
        ];
    }

    /**
     * Computes the content when a Log is deleted.
     */
    public static function logDeleted(array $content, Model $model): array
    {
        return [
            'deleted_count' => $content['deleted_count'] + 1,
        ];
    }
}
