<?php

namespace Laravelcargo\LaravelCargo\Tests;

use Laravelcargo\LaravelCargo\Models\Projection;
use Laravelcargo\LaravelCargo\Tests\Models\Log;
use Laravelcargo\LaravelCargo\Tests\Projectors\MultipleIntervalsProjector;

class ProjectionTest extends TestCase
{
    use WithProjectableFactory;

    /** @test */
    public function it_has_a_relationship_with_the_model()
    {
        Log::factory()->create();
        $projection = Projection::first();

        $this->assertNotEmpty($projection->from(Log::class)->get());
    }

    /** @test */
    public function it_get_all_the_projections_from_a_single_period()
    {
        $this->createModelWithProjectors(Log::class, [MultipleIntervalsProjector::class]); // 1
        $this->createModelWithProjectors(Log::class, [MultipleIntervalsProjector::class]); // 1
        $this->travel(6)->minutes();
        $this->createModelWithProjectors(Log::class, [MultipleIntervalsProjector::class]); // 2

        $numberOf5MinutesProjections = Projection::period('5 minutes')->count();

        $this->assertEquals($numberOf5MinutesProjections, 2);
    }
}
