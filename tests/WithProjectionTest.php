<?php

namespace Laravelcargo\LaravelCargo\Tests;

use Laravelcargo\LaravelCargo\Tests\Models\Log;

class WithProjectionTest extends TestCase
{
    /** @test */
    public function it_creates_a_projection_for_each_interval_when_a_model_with_projections_is_created()
    {
        /** @var Log $log */
        $log = Log::factory()->create();
        $numberOfIntervals = $log->getIntervalCount();

        $this->assertDatabaseCount('cargo_projections', $numberOfIntervals);
    }
}
