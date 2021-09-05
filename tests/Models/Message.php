<?php

namespace Laravelcargo\LaravelCargo\Tests\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravelcargo\LaravelCargo\Tests\Projectors\SingleIntervalProjector;
use Laravelcargo\LaravelCargo\WithProjections;

class Message extends Model
{
    use HasFactory;
    use WithProjections;

    /**
     * The lists of the projectors.
     */
    protected array $projectors = [SingleIntervalProjector::class];
}
