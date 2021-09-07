<?php

namespace Laravelcargo\LaravelCargo\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

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
     * Get all the models from the projection.
     */
    public function from(string $modelName): MorphToMany
    {
        return $this->morphedByMany($modelName, 'projectable', 'cargo_projectables');
    }

    /**
     * Scope a query to filter by name.
     */
    public function scopeName(Builder $query, string $name): Builder
    {
        return $query->where('name', $name);
    }

    /**
     * Scope a query to filter by period.
     */
    public function scopePeriod(Builder $query, string $period): Builder
    {
        return $query->where('period', $period);
    }

    /**
     * Scope a query to filter by key.
     */
    public function scopeKey(Builder $query, array|string|int $keys): Builder
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
}
