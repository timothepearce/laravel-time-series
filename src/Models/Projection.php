<?php

namespace Laravelcargo\LaravelCargo\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Laravelcargo\LaravelCargo\Exceptions\MissingProjectionNameException;
use Laravelcargo\LaravelCargo\Exceptions\MissingProjectionPeriodException;
use Laravelcargo\LaravelCargo\ProjectionCollection;

class Projection extends Model
{
    use HasFactory;

    protected $table = 'cargo_projections';

    protected $guarded = [];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'content' => 'json',
    ];

    /**
     * The projection's name used in query.
     */
    protected string | null $queryName = null;

    /**
     * The projection's period used in query.
     */
    protected string | null $queryPeriod = null;

    /**
     * Create a new Eloquent Collection instance.
     */
    public function newCollection(array $models = []): Collection
    {
        return new ProjectionCollection($models);
    }

    /**
     * Get all the models from the projection.
     */
    public function from(string $modelName): MorphToMany
    {
        return $this->morphedByMany($modelName, 'projectable', 'cargo_projectables');
    }

    /**
     * Scope a query to filter by name.
     */
    public function scopeFromProjector(Builder $query, string $projectorName): Builder
    {
        $this->queryName = $projectorName;

        return $query->where('projector_name', $projectorName);
    }

    /**
     * Scope a query to filter by period.
     */
    public function scopePeriod(Builder $query, string $period): Builder
    {
        $this->queryPeriod = $period;

        return $query->where('period', $period);
    }

    /**
     * Scope a query to filter by key.
     */
    public function scopeKey(Builder $query, array | string | int $keys): Builder
    {
        if (gettype($keys) === 'array') {
            return $query->where(function ($query) use (&$keys) {
                collect($keys)->each(function ($key, $index) use (&$query) {
                    return $index === 0 ?
                        $query->where('key', (string) $key) :
                        $query->orWhere('key', (string) $key);
                });
            });
        }

        return $query->where('key', (string) $keys);
    }

    /**
     * Scope a query to filter by the given dates
     * @throws MissingProjectionPeriodException
     * @throws MissingProjectionNameException
     */
    public function scopeBetween(Builder $query, Carbon $startDate, Carbon $endDate): Builder
    {
        if (is_null($this->queryName)) {
            throw new MissingProjectionNameException();
        }

        if (is_null($this->queryPeriod)) {
            throw new MissingProjectionPeriodException();
        }

        [$quantity, $periodType] = Str::of($this->queryPeriod)->split('/[\s]+/');

        return $query->whereBetween('start_date', [
            $startDate->floorUnit($periodType, $quantity),
            $endDate->floorUnit($periodType, $quantity),
        ]);
    }

    /**
     * Scope a query to filter by the given dates and fill with empty period if necessary.
     */
    public function scopeFillBetween(Builder $query, Carbon $startDate, Carbon $endDate): ProjectionCollection
    {
        $projections = $query->between($startDate, $endDate)->get();

        return $projections->fillBetween(
            $this->queryName,
            $this->queryPeriod,
            $startDate,
            $endDate,
        );
    }
}
