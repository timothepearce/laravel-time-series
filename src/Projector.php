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
     * Parse the given period
     */
    private function parsePeriod(string $period): void
    {
        [$quantity, $periodType] = Str::of($period)->split('/[\s]+/');

        $this->findOrCreateProjection($period, (int) $quantity, $periodType);
    }

    /**
     * Find or create the projection.
     */
    private function findOrCreateProjection(string $period, int $quantity, string $periodType): void
    {
//        $projection = $this->model
//            ->projections(self::class, $period)
//            ->firstWhere('start_date', Carbon::now()->floorUnit($periodType, $quantity));
//
//        if (is_null($projection)) { // createProjection }
        $projection = Projection::firstOrNew([
            'name' => $this::class,
            'period' => $period,
            'start_date' => Carbon::now()->floorUnit($periodType, $quantity),
        ], ['content' => $this->defaultContent()]);

        $projection->content = $this->handle($projection);

        $projection->save();

        $this->model->projections()->attach($projection);
    }

    /**
     * Set the periods.
     */
    public function setPeriods(array $newPeriods): void
    {
        $this->periods = $newPeriods;
    }
}
