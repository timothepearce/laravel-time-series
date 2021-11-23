<?php

namespace TimothePearce\Quasar\Tests\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use TimothePearce\Quasar\Models\Traits\Projectable;
use TimothePearce\Quasar\Tests\Projectors\SinglePeriodProjector;

class Message extends Model
{
    use HasFactory, Projectable;

    /**
     * The projections list.
     */
    protected array $projections = [SinglePeriodProjector::class];
}
