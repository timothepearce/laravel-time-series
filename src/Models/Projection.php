<?php

namespace TimothePearce\Quasar\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Carbon;
use TimothePearce\Quasar\Collections\ProjectionCollection;
use TimothePearce\Quasar\Exceptions\MissingProjectionNameException;
use TimothePearce\Quasar\Exceptions\MissingProjectionPeriodException;
use TimothePearce\Quasar\Models\Scopes\ProjectionScope;
use TimothePearce\Quasar\Quasar;

class Projection extends Model
{
    use HasFactory;

    protected $table = 'quasar_projections';

    protected $guarded = [];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'content' => 'json',
        'start_date' => 'datetime',
    ];

    /**
     * The projection name used in query.
     */
    protected string|null $projectionName = null;

    /**
     * The projection's period used in query.
     */
    protected string|null $queryPeriod = null;

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::addGlobalScope(new ProjectionScope());
    }

    /**
     * Creates a new Eloquent Collection instance.
     */
    public function newCollection(array $models = []): Collection
    {
        return new ProjectionCollection($models);
    }

    /**
     * Gets the relationship with the projectable models.
     */
    public function from(string $modelName): MorphToMany
    {
        return $this->morphedByMany($modelName, 'projectable', 'quasar_projectables');
    }

    /**
     * Converts the projection to a time-series segment.
     */
    public function toSegment(): array
    {
        return [
            'projection_name' => $this->projection_name,
            'period' => $this->period,
            'start_date' => $this->start_date->toDateTimeString(),
            'end_date' => $this->end_date->toDateTimeString(),
            'content' => $this->content,
        ];
    }

    /**
     * Scopes a query to filter by name.
     */
    public function scopeName(Builder $query, string $projectorName): Builder
    {
        $this->projectionName = $projectorName;

        return $query->where('projection_name', $projectorName);
    }

    /**
     * Scopes a query to filter by period.
     */
    public function scopePeriod(Builder $query, string $period): Builder
    {
        $this->queryPeriod = $period;

        return $query->where('period', $period);
    }

    /**
     * Scopes a query to filter by key.
     */
    public function scopeKey(Builder $query, array|string|int $keys): Builder
    {
        if (is_array($keys)) {
            return $query->where(function ($query) use (&$keys) {
                collect($keys)->each(function ($key, $index) use (&$query) {
                    return $index === 0 ?
                        $query->where('key', (string)$key) :
                        $query->orWhere('key', (string)$key);
                });
            });
        }

        return $query->where('key', (string)$keys);
    }

    /**
     * Scopes a query to filter by the given dates
     * @throws MissingProjectionNameException
     * @throws MissingProjectionPeriodException
     */
    public function scopeBetween(Builder $query, Carbon $startDate, Carbon $endDate): Builder
    {
        $this->resolveProjectionName();

        if (is_null($this->queryPeriod)) {
            throw new MissingProjectionPeriodException();
        }

        [$betweenStartDate, $betweenEndDate] = [$this->resolveFloorDate($startDate), $this->resolveFloorDate($endDate)];

        return $query->whereBetween('start_date', [
            $betweenStartDate,
            $betweenEndDate,
        ])->where('start_date', '!=', $betweenEndDate);
    }

    /**
     * Scopes a query to filter by the given dates and fill with empty period if necessary.
     * @throws MissingProjectionNameException
     * @throws MissingProjectionPeriodException
     */
    public function scopeFillBetween(Builder $query, Carbon $startDate, Carbon $endDate): ProjectionCollection
    {
        $projections = $query->between($startDate, $endDate)->get();

        return $projections->fillBetween(
            $startDate,
            $endDate,
            $this->resolveProjectionName(),
            $this->queryPeriod,
        );
    }

    /**
     * Constraints the query to the fill between scope, then executes it and converts the results to a time-series.
     * @throws MissingProjectionNameException
     * @throws MissingProjectionPeriodException
     */
    public function scopeToTimeSeries(Builder $query, Carbon $startDate, Carbon $endDate): ProjectionCollection
    {
        return $query->fillBetween($startDate, $endDate)
            ->toTimeSeries($startDate, $endDate);
    }

    /**
     * Gets the end_date attribute.
     */
    public function getEndDateAttribute(): Carbon
    {
        return $this->start_date->add($this->period)->subSecond();
    }

    /**
     * Resolves the projection name.
     * @throws MissingProjectionNameException
     */
    private function resolveProjectionName(): string
    {
        if (! is_null($this->projectionName)) {
            return $this->projectionName;
        }

        if ($this->callFromChild()) {
            return get_called_class();
        }

        throw new MissingProjectionNameException();
    }

    /**
     * Resolves the floor date.
     */
    private function resolveFloorDate(Carbon $date): Carbon
    {
        return app(Quasar::class)->resolveFloorDate($date->copy(), $this->queryPeriod);
    }

    /**
     * Asserts the call is made from the child class.
     */
    private function callFromChild(): bool
    {
        return self::class !== get_called_class();
    }
}
