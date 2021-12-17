<?php

namespace TimothePearce\Quasar\Tests\Commands;

use Illuminate\Support\Facades\Artisan;
use TimothePearce\Quasar\Tests\Models\Log;
use TimothePearce\Quasar\Tests\Models\Projections\MultiplePeriodsProjection;
use TimothePearce\Quasar\Tests\Models\Projections\SinglePeriodProjection;
use TimothePearce\Quasar\Tests\ProjectableFactory;
use TimothePearce\Quasar\Tests\TestCase;

class DropProjectionsCommandTest extends TestCase
{
    use ProjectableFactory;

    /** @test */
    public function it_drops_all_the_projections()
    {
        $this->createModelWithProjections(Log::class, [
            SinglePeriodProjection::class,
            MultiplePeriodsProjection::class,
        ]);
        $this->assertDatabaseCount('quasar_projections', 9);

        Artisan::call("quasar:drop");

        $this->assertDatabaseCount('quasar_projections', 0);
    }
}
