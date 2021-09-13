<?php

namespace Laravelcargo\LaravelCargo;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

class ProjectionCollection extends Collection
{
    /**
     * Fills the collection with empty projection between the given dates.
     *
     */
    public function fillBetween(string $projectionName, string $period, Carbon $startDate, Carbon $endDate)
    {
        return $this;
    }
}
