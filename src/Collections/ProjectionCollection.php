<?php

namespace TimothePearce\Quasar\Collections;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use TimothePearce\Quasar\Exceptions\EmptyProjectionCollectionException;
use TimothePearce\Quasar\Exceptions\MultiplePeriodsException;
use TimothePearce\Quasar\Exceptions\MultipleProjectionsException;
use TimothePearce\Quasar\Exceptions\OverlappingFillBetweenDatesException;
use TimothePearce\Quasar\Models\Projection;

class ProjectionCollection extends Collection
{
    /**
     * Fills the collection with empty projection between the given dates.
     *
     * @throws MultipleProjectionsException|MultiplePeriodsException|EmptyProjectionCollectionException|OverlappingFillBetweenDatesException
     */
    public function fillBetween(
        Carbon      $startDate,
        Carbon      $endDate,
        string|null $projectorName = null,
        string|null $period = null,
    ): ProjectionCollection
    {
        [$projectorName, $period] = $this->resolveGuessParameters($projectorName, $period);
        [$startDate, $endDate] = $this->resolveDatesParameters($period, $startDate, $endDate);

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
     * Validates and resolve the guess parameters.
     *
     * @throws EmptyProjectionCollectionException|MultipleProjectionsException|MultiplePeriodsException
     */
    private function resolveGuessParameters(string|null $projectorName, string|null $period): array
    {
        if ($this->count() === 0 && $this->shouldGuessParameters($projectorName, $period)) {
            throw new EmptyProjectionCollectionException();
        }

        return [$this->resolveProjectorName($projectorName), $this->resolvePeriod($period)];
    }

    /**
     * Validates and resolve the dates parameters.
     *
     * @throws OverlappingFillBetweenDatesException
     */
    private function resolveDatesParameters(string $period, Carbon $startDate, Carbon $endDate): array
    {
        [$periodQuantity, $periodType] = Str::of($period)->split('/[\s]+/');

        $startDate->floorUnit($periodType, $periodQuantity);
        $endDate->floorUnit($periodType, $periodQuantity);

        if ($startDate->greaterThanOrEqualTo($endDate)) {
            throw new OverlappingFillBetweenDatesException();
        }

        return [$startDate, $endDate];
    }

    /**
     * Asserts the parameters should be guessed.
     */
    private function shouldGuessParameters(string|null $projectorName, string|null $period): bool
    {
        return is_null($projectorName) || is_null($period);
    }

    /**
     * Resolves the projector name.
     *
     * @throws MultipleProjectionsException|EmptyProjectionCollectionException
     */
    private function resolveProjectorName(string|null $projectorName): string
    {
        $this->assertUniqueProjectorName();

        return $projectorName ?? $this->guessProjectorName();
    }

    /**
     * Resolve the period.
     *
     * @throws MultiplePeriodsException
     */
    private function resolvePeriod(string|null $period): string
    {
        $this->assertUniquePeriod();

        return $period ?? $this->guessPeriod();
    }

    /**
     * Asserts the given projections came from a single type of projector.
     *
     * @throws MultipleProjectionsException
     */
    private function assertUniqueProjectorName()
    {
        $projectorNames = $this->unique('projection_name');

        if ($projectorNames->count() > 1) {
            throw new MultipleProjectionsException();
        }
    }

    /**
     * Asserts the given projections has a single type of period.
     *
     * @throws MultiplePeriodsException
     */
    private function assertUniquePeriod()
    {
        $periodNames = $this->unique('period');

        if ($periodNames->count() > 1) {
            throw new MultiplePeriodsException();
        }
    }

    /**
     * Guess the projector name.
     *
     * @throws EmptyProjectionCollectionException
     */
    private function guessProjectorName(): string
    {
        return $this->first()->projection_name;
    }

    /**
     * Guess the period.
     */
    private function guessPeriod(): string
    {
        return $this->first()->period;
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

            if ($cursorDate->notEqualTo($endDate)) {
                $allProjectionsDates->push(clone $cursorDate);
            }
        endwhile;

        return $allProjectionsDates;
    }

    /**
     * Makes an empty projection from the given projector name.
     */
    private function makeEmptyProjection(string $projectorName, string $period, string $startDate): Projection
    {
        return Projection::make([
            'projection_name' => $projectorName,
            'key' => null,
            'period' => $period,
            'start_date' => $startDate,
            'content' => (new $projectorName)->defaultContent(),
        ]);
    }
}
