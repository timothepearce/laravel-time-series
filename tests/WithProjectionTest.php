<?php

namespace Laravelcargo\LaravelCargo\Tests;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Queue;
use Laravelcargo\LaravelCargo\Jobs\ProcessProjection;
use Laravelcargo\LaravelCargo\Models\Projection;
use Laravelcargo\LaravelCargo\Tests\Models\Log;
use Laravelcargo\LaravelCargo\Tests\Projectors\MultipleIntervalsProjector;

class WithProjectionTest extends TestCase
{
    use WithProjectableFactory;

    /** @test */
    public function it_creates_a_projection_for_each_interval_when_a_model_with_projections_is_created()
    {
        $this->createModelWithProjectors(Log::class, [MultipleIntervalsProjector::class]);

        $this->assertDatabaseCount('cargo_projections', 8);
    }

    /** @test */
    public function it_get_the_projection_when_the_interval_is_in_completion()
    {
        $this->travelTo(Carbon::today());
        Log::factory()->create();

        $this->travel(3)->minutes();
        Log::factory()->create();

        $this->assertDatabaseCount('cargo_projections', 1);
    }

    /** @test */
    public function it_creates_a_new_projection_when_the_interval_is_ended()
    {
        $this->travelTo(Carbon::today());
        Log::factory()->create();

        $this->travel(6)->minutes();
        Log::factory()->create();

        $this->assertDatabaseCount('cargo_projections', 2);
    }

    /** @test */
    public function it_computes_the_content_of_the_projection_from_the_default_one()
    {
        Log::factory()->create();

        $this->assertEquals(1, Projection::first()->content["number of logs"]);
    }

    /** @test */
    public function it_computes_the_content_of_the_projection()
    {
        Log::factory()->count(2)->create();

        $this->assertEquals(2, Projection::first()->content["number of logs"]);
    }

    /** @test */
    public function it_dispatch_a_job_when_the_queue_config_is_enabled()
    {
        Queue::fake();
        config(['cargo.queue' => true]);

        Log::factory()->create();

        Queue::assertPushed(ProcessProjection::class);
    }

    /** @test */
    public function it_dispatch_a_job_to_the_named_queue()
    {
        Queue::fake();
        config(['cargo.queue' => true, 'cargo.queue_name' => 'named']);

        Log::factory()->create();

        Queue::assertPushedOn('named', ProcessProjection::class);
    }

    /** @test */
    public function it_has_a_relationship_with_the_projection()
    {
        $log = Log::factory()->create();

        $this->assertNotEmpty($log->projections);
    }
}
