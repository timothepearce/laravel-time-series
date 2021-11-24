<?php

namespace TimothePearce\Quasar;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use ReflectionException;
use ReflectionProperty;
use TimothePearce\Quasar\Models\Projection;

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
     * Parses the periods defined as class attribute.
     * @throws ReflectionException
     */
    public function parsePeriods(): void
    {
        $periods = (new ReflectionProperty($this->projectionName, 'periods'))->getValue();

        collect($periods)->each(fn(string $period) => $this->parsePeriod($period));
    }

    /**
     * Parses the given period.
     */
    private function parsePeriod(string $period): void
    {
        [$quantity, $periodType] = Str::of($period)->split('/[\s]+/');

        $projection = $this->findProjection($period, (int)$quantity, $periodType);

        is_null($projection) ?
            $this->createProjection($period, (int)$quantity, $periodType) :
            $this->updateProjection($projection);
    }

    /**
     * Try to find the projection.
     */
    private function findProjection(string $period, int $quantity, string $periodType): Projection|null
    {
        $query = Projection::where([
            ['projection_name', $this->projectionName],
            ['key', $this->hasKey() ? $this->key() : null],
            ['period', $period],
            ['start_date', Carbon::now()->floorUnit($periodType, $quantity)],
        ]);

        return $query->first();
    }

    /**
     * Creates the projection.
     */
    private function createProjection(string $period, int $quantity, string $periodType): void
    {
        $this->projectedModel->projections()->create([
            'projection_name' => $this->projectionName,
            'key' => $this->hasKey() ? $this->key() : null,
            'period' => $period,
            'start_date' => Carbon::now()->floorUnit($periodType, $quantity),
            'content' => $this->getProjectedContent($this->projectionName::defaultContent()),
        ]);
    }

    /**
     * Updates the projection.
     */
    private function updateProjection(Projection $projection): void
    {
        $projection->content = $this->getProjectedContent($projection->content);

        $projection->save();
    }

    /**
     * Determines whether the class has a key.
     */
    private function hasKey(): bool
    {
        return method_exists($this->projectionName, 'key');
    }

    /**
     * The key used to query the projection.
     */
    public function key(): bool|int|string
    {
        return $this->projectionName::key($this->projectedModel);
    }

    /**
     * Get the projected content.
     */
    private function getProjectedContent(array $baseContent): array
    {
        return $this->projectionName::handle($baseContent, $this->projectedModel);
    }
}
