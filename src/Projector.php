<?php

namespace Laravelcargo\LaravelCargo;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Laravelcargo\LaravelCargo\Models\Projection;

abstract class Projector
{
    protected Model $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * Parse the periods defined as class attribute.
     */
    public function parsePeriods(): void
    {
        collect($this->periods)->each(fn (string $period) => $this->parsePeriod($period));
    }

    /**
     * Parses the given period
     */
    private function parsePeriod(string $period): void
    {
        [$quantity, $periodType] = Str::of($period)->split('/[\s]+/');

        $projection = $this->findProjection($period, (int) $quantity, $periodType);

        is_null($projection) ?
            $this->createProjection($period, (int) $quantity, $periodType) :
            $this->updateProjection($projection);
    }

    /**
     * Try to find the projection.
     */
    private function findProjection(string $period, int $quantity, string $periodType): Projection | null
    {
        return Projection::firstWhere([
            ['name', $this::class],
            ['period', $period],
            ['start_date', Carbon::now()->floorUnit($periodType, $quantity)],
        ]);

//        return $this->model
//            ->projections($this::class, $period)
//            ->where('start_date', Carbon::now()->floorUnit($periodType, $quantity))
//            ->first();
    }

    /**
     * Creates the projection.
     */
    private function createProjection(string $period, int $quantity, string $periodType): void
    {
        $this->model->projections()->create([
            'name' => $this::class,
            'period' => $period,
            'start_date' => Carbon::now()->floorUnit($periodType, $quantity),
            'content' => $this->handle($this->defaultContent(), $this->model)
        ]);
    }

    /**
     * Updates the projection.
     */
    private function updateProjection(Projection $projection): void
    {
        $projection->content = $this->handle($projection->content, $this->model);

        $projection->save();
    }

    /**
     * Set the periods.
     */
    public function setPeriods(array $newPeriods): void
    {
        $this->periods = $newPeriods;
    }
}
