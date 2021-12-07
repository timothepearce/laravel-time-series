<?php

namespace TimothePearce\Quasar;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use ReflectionException;
use ReflectionProperty;
use TimothePearce\Quasar\Exceptions\MissingCallableMethodException;
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
     * @throws MissingCallableMethodException
     */
    public function parsePeriods(): void
    {
        $periods = (new ReflectionProperty($this->projectionName, 'periods'))->getValue();

        collect($periods)->each(function ($period) {
            $this->isGlobalPeriod($period) ?
                $this->createOrUpdateGlobalPeriod() :
                $this->parsePeriod($period);
        });
    }

    /**
     * Is the given period a global one or not.
     */
    private function isGlobalPeriod($period): bool
    {
        return $period === '*';
    }

    /**
     * Handles the global period case.
     * @throws MissingCallableMethodException
     */
    private function createOrUpdateGlobalPeriod(): void
    {
        ray(Projection::all());
        $projection = $this->findGlobalProjection();

        is_null($projection) ?
            $this->createGlobalProjection() :
            $this->updateProjection($projection);
    }

    /**
     * Parses the given period.
     * @throws MissingCallableMethodException
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
     * Finds the global projection if it exists.
     */
    private function findGlobalProjection(): Projection|null
    {
        return Projection::firstWhere([
            ['projection_name', $this->projectionName],
            ['key', $this->hasKey() ? $this->key() : null],
            ['period', '*'],
            ['start_date', null],
        ]);
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
     * @throws MissingCallableMethodException
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
     * Creates the global projection.
     * @throws MissingCallableMethodException
     */
    private function createGlobalProjection()
    {
        $this->projectedModel->projections()->create([
            'projection_name' => $this->projectionName,
            'key' => $this->hasKey() ? $this->key() : null,
            'period' => '*',
            'start_date' => null,
            'content' => $this->mergeProjectedContent($this->projectionName::defaultContent()),
        ]);
    }

    /**
     * Updates the projection.
     * @throws MissingCallableMethodException
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
     * @throws MissingCallableMethodException
     */
    private function mergeProjectedContent(array $content): array
    {
        return array_merge($content, $this->resolveCallableMethod($content));
    }

    /**
     * Resolves the callable method.
     * @throws MissingCallableMethodException
     */
    private function resolveCallableMethod(array $content): array
    {
        $modelName = Str::of($this->projectedModel::class)->explode('\\')->last();
        $callableMethod = lcfirst($modelName) . ucfirst($this->eventName);
        $defaultCallable = 'projectable' . ucfirst($this->eventName);

        if (method_exists($this->projectionName, $callableMethod)) {
            return $this->projectionName::$callableMethod($content, $this->projectedModel);
        }

        if (method_exists($this->projectionName, $defaultCallable)) {
            return $this->projectionName::$defaultCallable($content, $this->projectedModel);
        }

        throw new MissingCallableMethodException();
    }
}
