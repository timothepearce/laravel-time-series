<?php

namespace TimothePearce\Quasar\Tests\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use TimothePearce\Quasar\Models\Traits\Projectable;
use TimothePearce\Quasar\Tests\Models\Projections\SinglePeriodProjection;

class Log extends Model
{
    use HasFactory;
    use Projectable;

    protected $guarded = [];

    /**
     * The projections list.
     */
    protected array $projections = [SinglePeriodProjection::class];
}
