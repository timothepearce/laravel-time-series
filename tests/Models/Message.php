<?php

namespace TimothePearce\TimeSeries\Tests\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use TimothePearce\TimeSeries\Models\Traits\Projectable;
use TimothePearce\TimeSeries\Tests\Models\Projections\SinglePeriodProjection;

class Message extends Model
{
    use HasFactory;
    use Projectable;
    use SoftDeletes;

    /**
     * The projections list.
     */
    protected array $projections = [SinglePeriodProjection::class];
    public string $dateColumn = 'created_at';
}
