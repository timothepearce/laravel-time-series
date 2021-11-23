<?php

namespace TimothePearce\Quasar\Contracts;

use Illuminate\Database\Eloquent\Model;

interface ProjectionContract
{
    /**
     * The default projection content.
     */
    public static function defaultContent(): array;

    /**
     * Compute the projection.
     */
    public static function handle(array $content, Model $model): array;
}
