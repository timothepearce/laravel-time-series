<?php

namespace TimothePearce\Quasar;

use Illuminate\Database\Eloquent\Model;
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

    public function __construct(
        protected Model  $projectedModel,
        protected string $projectionName,
        protected string $eventName
    ) {
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

        $projection = $this->findProjection($period, (int)$quantity, $periodType);

        is_null($projection) ?
            $this->createProjection($period, (int)$quantity, $periodType) :
            $this->updateProjection($projection);
    }

    /**
     * Finds the projection if it exists.
     */
    private function findProjection(string $period, int $quantity, string $periodType): Projection|null
    {
        return Projection::firstWhere([
            ['projection_name', $this->projectionName],
            ['key', $this->hasKey() ? $this->key() : null],
            ['period', $period],
            ['start_date', $this->projectedModel->created_at->floorUnit($periodType, $quantity)],
        ]);
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
            'start_date' => $this->projectedModel->created_at->floorUnit($periodType, $quantity),
            'content' => $this->mergeProjectedContent($this->projectionName::defaultContent()),
        ]);
    }

    /**
     * Updates the projection.
     */
    private function updateProjection(Projection $projection): void
    {
        $projection->content = $this->mergeProjectedContent($projection->content);

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
     * Merges the projected content with the given one.
     */
    private function mergeProjectedContent(array $content): array
    {
        return array_merge($content, $this->resolveCallableMethod($content));
    }

    /**
     * Resolves the callable method.
     */
    private function resolveCallableMethod(array $content): array
    {
        $modelName = Str::of($this->projectedModel::class)->explode('\\')->last();
        $callableMethod = lcfirst($modelName) . ucfirst($this->eventName);
        $defaultCallable = 'projectable' . ucfirst($this->eventName);

        return method_exists($this->projectionName, $callableMethod) ?
            $this->projectionName::$callableMethod($content, $this->projectedModel) :
            $this->projectionName::$defaultCallable($content, $this->projectedModel);
    }
}
