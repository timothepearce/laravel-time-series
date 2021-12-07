<?php

namespace TimothePearce\Quasar\Tests\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use TimothePearce\Quasar\Models\Traits\Projectable;
use TimothePearce\Quasar\Tests\Models\Projections\SinglePeriodProjection;

class Message extends Model
{
    use HasFactory;
    use Projectable;
    use SoftDeletes;

    /**
     * The projections list.
     */
    protected array $projections = [SinglePeriodProjection::class];
}
