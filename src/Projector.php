<?php

namespace Laravelcargo\LaravelCargo;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Laravelcargo\LaravelCargo\Models\Projection;
use ReflectionException;
use ReflectionProperty;

class Projector
{
    /**
     * Lists the time intervals used to compute the projections.
     */
    protected array $periods;

    public function __construct(protected Model $projectedModel, protected string $projectionName)
    {
    }

    /**
     * The key used to query the projection.
     */
    public function key(Model $model): bool | int | string
    {
        return false;
    }

    /**
     * Parses the periods defined as class attribute.
     * @throws ReflectionException
     */
    public function parsePeriods(): void
    {
        $periods = (new ReflectionProperty($this->projectionName, 'periods'))->getValue();

        collect($periods)->each(fn (string $period) => $this->parsePeriod($period));
    }

    /**
     * Parses the given period.
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
            ['projector_name', $this::class],
            ['key', $this->hasKey() ? $this->key($this->projectedModel) : null],
            ['period', $period],
            ['start_date', Carbon::now()->floorUnit($periodType, $quantity)],
        ]);
    }

    /**
     * Creates the projection.
     */
    private function createProjection(string $period, int $quantity, string $periodType): void
    {
        $this->projectedModel->projections()->create([
            'projector_name' => $this::class,
            'key' => $this->hasKey() ? $this->key($this->projectedModel) : null,
            'period' => $period,
            'start_date' => Carbon::now()->floorUnit($periodType, $quantity),
            'content' => $this->getProjectedContent(),
        ]);
    }

    /**
     * Updates the projection.
     */
    private function updateProjection(Projection $projection): void
    {
        $projection->content = $this->getProjectedContent();

        $projection->save();
    }

    /**
     * Determines whether the class has a key.
     */
    private function hasKey(): bool
    {
        return $this->key($this->projectedModel) !== false;
    }

    /**
     * Get the projected content.
     */
    private function getProjectedContent(): array
    {
        return $this->projectionName::handle($this->projectionName::defaultContent(), $this->projectedModel);
    }
}
