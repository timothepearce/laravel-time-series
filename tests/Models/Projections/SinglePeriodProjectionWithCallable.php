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
    public static array $periods = ['5 minutes'];

    /**
     * The default projection content.
     */
    public static function defaultContent(): array
    {
        return [
            'log_created_count' => 0,
            'log_updated_count' => 0,
            'log_deleted_count' => 0,
        ];
    }

    /**
     * Computes the content when a Log is created.
     */
    public static function logCreated(array $content, Model $model): array
    {
        return [
            'log_created_count' => $content['log_created_count'] + 1,
        ];
    }

    /**
     * Computes the content when a Log is updated.
     */
    public static function logUpdated(array $content, Model $model): array
    {
        return [
            'log_updated_count' => $content['log_updated_count'] + 1,
        ];
    }

    /**
     * Computes the content when a Log is deleted.
     */
    public static function logDeleted(array $content, Model $model): array
    {
        return [
            'log_deleted_count' => $content['log_deleted_count'] + 1,
        ];
    }
}
