<?php

namespace TimothePearce\Quasar\Tests\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use TimothePearce\Quasar\Tests\Projectors\SinglePeriodProjector;
use TimothePearce\Quasar\WithProjections;

class Message extends Model
{
    use HasFactory;
    use WithProjections;

    /**
     * The lists of the projectors.
     */
    protected array $projections = [SinglePeriodProjector::class];
}
