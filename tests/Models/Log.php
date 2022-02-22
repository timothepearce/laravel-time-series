<?php

namespace TimothePearce\TimeSeries\Tests\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use TimothePearce\TimeSeries\Models\Traits\Projectable;
use TimothePearce\TimeSeries\Tests\Models\Projections\SinglePeriodProjection;

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
