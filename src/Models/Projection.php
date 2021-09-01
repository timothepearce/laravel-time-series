<?php

namespace Laravelcargo\LaravelCargo\Models;

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
}
