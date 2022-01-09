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
     * Converts the collection to a time-series made of 'segments'.
     *
     * @throws EmptyProjectionCollectionException|MultiplePeriodsException|MultipleProjectionsException|OverlappingFillBetweenDatesException
     */
    public function toTimeSeries(
        Carbon      $startDate,
        Carbon      $endDate,
        string|null $projectionName = null,
        string|null $period = null,
    ): self {
        $projections = $this->fillBetween($startDate, $endDate, $projectionName, $period);

        return new self($projections->map->segment());
    }

    /**
     * Fills the collection with empty projection between the given dates.
     *
     * @throws MultipleProjectionsException|MultiplePeriodsException|EmptyProjectionCollectionException|OverlappingFillBetweenDatesException
     */
    public function fillBetween(
        Carbon      $startDate,
        Carbon      $endDate,
        string|null $projectionName = null,
        string|null $period = null,
    ): self {
        [$projectionName, $period] = $this->resolveTypeParameters($projectionName, $period);
        [$startDate, $endDate] = $this->resolveDatesParameters($period, $startDate, $endDate);

        $allPeriods = $this->getAllPeriods($startDate, $endDate, $period);
        $allProjections = new self([]);

        $allPeriods->each(function (string $currentPeriod) use (&$projectionName, &$period, &$allProjections) {
            $projection = $this->firstWhere('start_date', $currentPeriod);

            $allProjections->push($projection ?? $this->makeEmptyProjection($projectionName, $period, $currentPeriod));
        });

        return $allProjections;
    }

    /**
     * Validates and resolves the type parameters.
     *
     * @throws EmptyProjectionCollectionException|MultipleProjectionsException|MultiplePeriodsException
     */
    private function resolveTypeParameters(string|null $projectionName, string|null $period): array
    {
        if ($this->count() === 0 && $this->shouldResolveTypeParameters($projectionName, $period)) {
            throw new EmptyProjectionCollectionException();
        }

        return [$this->resolveProjectionName($projectionName), $this->resolvePeriod($period)];
    }

    /**
     * Validates and resolves the dates parameters.
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
     * Asserts the parameters should be resolved.
     */
    private function shouldResolveTypeParameters(string|null $projectionName, string|null $period): bool
    {
        return is_null($projectionName) || is_null($period);
    }

    /**
     * Resolves the projection name.
     *
     * @throws MultipleProjectionsException
     */
    private function resolveProjectionName(string|null $projectionName): string
    {
        $this->assertUniqueProjectionName();

        return $projectionName ?? $this->guessProjectionName();
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
     * Asserts it is composed of a single type of projection.
     *
     * @throws MultipleProjectionsException
     */
    private function assertUniqueProjectionName()
    {
        $projectionNames = $this->unique('projection_name');

        if ($projectionNames->count() > 1) {
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
     * Guesses the projector name.
     */
    private function guessProjectionName(): string
    {
        return $this->first()->projection_name;
    }

    /**
     * Guesses the period.
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
    private function makeEmptyProjection(string $projectionName, string $period, string $startDate): Projection
    {
        return Projection::make([
            'projection_name' => $projectionName,
            'key' => null,
            'period' => $period,
            'start_date' => $startDate,
            'content' => (new $projectionName())->defaultContent(),
        ]);
    }
}
