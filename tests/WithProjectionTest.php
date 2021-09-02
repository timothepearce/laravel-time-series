<?php

namespace Laravelcargo\LaravelCargo\Tests;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Queue;
use Laravelcargo\LaravelCargo\Jobs\ProcessProjection;
use Laravelcargo\LaravelCargo\Models\Projection;
use Laravelcargo\LaravelCargo\Tests\Models\A;
use Laravelcargo\LaravelCargo\Tests\Models\B;

class WithProjectionTest extends TestCase
{
    /** @test */
    public function it_creates_a_projection_for_each_interval_when_a_model_with_projections_is_created()
    {
        A::factory()->create();

        $this->assertDatabaseCount('cargo_projections', 8);
    }

    /** @test */
    public function it_get_the_projection_when_the_interval_is_in_completion()
    {
        $this->travelTo(Carbon::today());
        B::factory()->create();

        $this->travel(3)->minutes();
        B::factory()->create();

        $this->assertDatabaseCount('cargo_projections', 1);
    }

    /** @test */
    public function it_creates_a_new_projection_when_the_interval_is_ended()
    {
        $this->travelTo(Carbon::today());
        B::factory()->create();

        $this->travel(6)->minutes();
        B::factory()->create();

        $this->assertDatabaseCount('cargo_projections', 2);
    }

    /** @test */
    public function it_computes_the_content_of_the_projection_from_the_default_one()
    {
        B::factory()->create();

        $this->assertEquals(1, Projection::first()->content["number of logs"]);
    }

    /** @test */
    public function it_computes_the_content_of_the_projection()
    {
        B::factory()->count(2)->create();

        $this->assertEquals(2, Projection::first()->content["number of logs"]);
    }

    /** @test */
    public function it_dispatch_a_job_when_the_queue_config_is_enabled()
    {
        Queue::fake();
        config(['cargo.queue' => true]);

        B::factory()->create();

        Queue::assertPushed(ProcessProjection::class);
    }

    /** @test */
    public function it_dispatch_a_job_to_the_named_queue()
    {
        Queue::fake();
        config(['cargo.queue' => true, 'cargo.queue_name' => 'named']);

        B::factory()->create();

        Queue::assertPushedOn('named', ProcessProjection::class);
    }

    /** @test */
    public function it_has_a_relationship_with_the_projection()
    {
        $log = A::factory()->create();

        $this->assertNotNull($log->projections);
    }
}
