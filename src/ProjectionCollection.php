<?php

namespace Laravelcargo\LaravelCargo;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Laravelcargo\LaravelCargo\Exceptions\MultiplePeriodsException;
use Laravelcargo\LaravelCargo\Exceptions\MultipleProjectorsException;
use Laravelcargo\LaravelCargo\Models\Projection;

class ProjectionCollection extends Collection
{
    /**
     * Fills the collection with empty projection between the given dates.
     */
    public function fillBetween(
        Carbon $startDate,
        Carbon $endDate,
        string | null $projectorName = null,
        string | null $period = null
    ) {
        if (is_null($projectorName)) {
            $projectorName = $this->guessProjectorName();
        }

        if (is_null($period)) {
            $period = $this->guessPeriod();
        }

        [$periodQuantity, $periodType] = Str::of($period)->split('/[\s]+/');

        $startDate->floorUnit($periodType, $periodQuantity);
        $endDate->floorUnit($periodType, $periodQuantity);

        $allPeriods = $this->getAllPeriods($startDate, $endDate, $period);
        $allProjections = new self([]);

        $allPeriods->each(function (string $projectionPeriod) use (&$projectorName, &$period, &$allProjections) {
            $projection = $this->firstWhere('start_date', $projectionPeriod);

            is_null($projection) ?
                $allProjections->push($this->makeEmptyProjection($projectorName, $period, $projectionPeriod)) :
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
        $allProjectionsDates->push(clone $cursorDate);
        endwhile;

        return $allProjectionsDates;
    }

    /**
     * Guess the projector name.
     *
     * @throws MultipleProjectorsException
     */
    private function guessProjectorName(): string
    {
        return 'projector_name';
    }

    /**
     * Guess the period.
     *
     * @throws MultiplePeriodsException
     */
    private function guessPeriod(): string
    {
        return 'period';
    }

    /**
     * Makes an empty projection from the given projector name.
     */
    private function makeEmptyProjection(string $projectorName, string $period, string $startDate): Projection
    {
        return Projection::make([
            'projector_name' => $projectorName,
            'key' => null,
            'period' => $period,
            'start_date' => $startDate,
            'content' => $projectorName::defaultContent(),
        ]);
    }
}
