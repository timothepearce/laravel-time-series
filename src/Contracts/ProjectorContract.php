<?php

namespace Laravelcargo\LaravelCargo\Contracts;

use Illuminate\Database\Eloquent\Model;

interface ProjectorContract
{
    /**
     * The default projection content.
     */
    public static function defaultContent(): array;

    /**
     * Compute the projection.
     */
    public function handle(array $content, Model $model): array;
}
