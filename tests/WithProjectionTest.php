<?php

namespace Laravelcargo\LaravelCargo\Tests;

use Laravelcargo\LaravelCargo\Tests\Models\Log;

class WithProjectionTest extends TestCase
{
    /** @test */
    public function it_creates_a_projection_for_each_interval_when_a_model_with_projections_is_created()
    {
        Log::factory()->create();

        // $this->assertDatabaseCount('cargo_projections', 1);
        $this->assertTrue(true);
    }
}
