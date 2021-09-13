<?php

namespace Laravelcargo\LaravelCargo;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Laravelcargo\LaravelCargo\Models\Projection;

class ProjectionCollection extends Collection
{
    /**
     * Fills the collection with empty projection between the given dates.
     */
    public function fillBetween(string $projectionName, string $period, Carbon $startDate, Carbon $endDate)
    {
        [$periodQuantity, $periodType] = Str::of($period)->split('/[\s]+/');

        $startDate->floorUnit($periodType, $periodQuantity);
        $endDate->floorUnit($periodType, $periodQuantity);

        $allPeriods = $this->getAllPeriods($startDate, $endDate, $period);
        $allProjections = new self([]);

        $allPeriods->each(function (Carbon $period) use (&$allProjections) {
            $projection = $this->firstWhere('start_date', $period);

            is_null($projection) ?
                $allProjections->push($this->makeEmptyProjection($startDate, $period)) :
                $allProjections->push($projection);
        });

        return $allProjections;
    }

    /**
     * Get the projections dates.
     */
    private function getAllPeriods(Carbon $startDate, Carbon $endDate, string $period): \Illuminate\Support\Collection
    {
        $cursorDate = clone $startDate;
        $allProjectionsDates = collect([$startDate]);
        [$periodQuantity, $periodType] = Str::of($period)->split('/[\s]+/');

        while ($cursorDate->notEqualTo($endDate)):
            $cursorDate->add($periodQuantity, $periodType);
            $allProjectionsDates->push($cursorDate);
        endwhile;

        return $allProjectionsDates;
    }

    private function makeEmptyProjection(Carbon $startDate, string $period)
    {
        return Projection::make([
            'name' => 'name',
            'key' => null,
            'period' => $period,
            'start_date' => $startDate,
            'content' => [],
        ]);
    }
}
