<?php

namespace TimothePearce\Quasar\Tests\Models\Projections;

use Illuminate\Database\Eloquent\Model;
use TimothePearce\Quasar\Contracts\ProjectionContract;
use TimothePearce\Quasar\Models\Projection;

class SinglePeriodProjection extends Projection implements ProjectionContract
{
    /**
     * Lists the time intervals used to compute the projections.
     */
    public static array $periods = ['5 minutes'];

    /**
     * The default projection content.
     */
    public function defaultContent(): array
    {
        return [
            'created_count' => 0,
            'updating_count' => 0,
            'updated_count' => 0,
            'deleting_count' => 0,
            'deleted_count' => 0,
        ];
    }

    /**
     * Computes the content when a projectable model is created.
     */
    public static function projectableCreated(array $content, Model $model): array
    {
        return [
            'created_count' => $content['created_count'] + 1,
        ];
    }

    /**
     * Computes the content when a projectable model is updating.
     */
    public static function projectableUpdating(array $content, Model $model): array
    {
        return [
            'updating_count' => $content['updating_count'] + 1,
        ];
    }

    /**
     * Computes the content when a projectable model is updated.
     */
    public static function projectableUpdated(array $content, Model $model): array
    {
        return [
            'updated_count' => $content['updated_count'] + 1,
        ];
    }

    /**
     * Computes the content when a projectable model is deleting.
     */
    public static function projectableDeleting(array $content, Model $model): array
    {
        return [
            'deleting_count' => $content['deleting_count'] + 1,
        ];
    }

    /**
     * Computes the content when a projectable model is deleted.
     */
    public static function projectableDeleted(array $content, Model $model): array
    {
        return [
            'deleted_count' => $content['deleted_count'] + 1,
        ];
    }
}
