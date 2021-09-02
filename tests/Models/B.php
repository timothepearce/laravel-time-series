<?php

namespace Laravelcargo\LaravelCargo\Tests\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravelcargo\LaravelCargo\Tests\Projectors\BProjector;
use Laravelcargo\LaravelCargo\WithProjections;

class B extends Model
{
    use HasFactory;
    use WithProjections;

    protected $table = 'b';

    /**
     * The lists of the projectors.
     */
    protected array $projectors = [BProjector::class];
}
