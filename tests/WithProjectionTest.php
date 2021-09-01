<?php

namespace Laravelcargo\LaravelCargo\Tests;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Queue;
use Laravelcargo\LaravelCargo\Jobs\ProcessProjection;
use Laravelcargo\LaravelCargo\Models\Projection;
use Laravelcargo\LaravelCargo\Tests\Models\Log;

class WithProjectionTest extends TestCase
{
    /** @test */
    public function it_get_the_projection_when_the_interval_is_in_completion()
    {
        $intervals = ['5 minutes'];
        $this->travelTo(Carbon::today());
        $this->createModelWithIntervals(Log::class, $intervals);

        $this->travel(3)->minutes();
        $this->createModelWithIntervals(Log::class, $intervals);

        $this->assertDatabaseCount('cargo_projections', 1);
    }

    /** @test */
    public function it_creates_a_new_projection_when_the_interval_is_ended()
    {
        $intervals = ['5 minutes'];
        $this->travelTo(Carbon::today());
        $this->createModelWithIntervals(Log::class, $intervals);

        $this->travel(6)->minutes();
        $this->createModelWithIntervals(Log::class, $intervals);

        $this->assertDatabaseCount('cargo_projections', 2);
    }

    /** @test */
    public function it_creates_a_projection_for_each_interval_when_a_model_with_projections_is_created()
    {
        /** @var Log $log */
        $log = Log::factory()->create();
        $numberOfIntervals = $log->getIntervalCount();

        $this->assertDatabaseCount('cargo_projections', $numberOfIntervals);
    }

    /** @test */
    public function it_computes_the_content_of_the_projection_from_the_default_one()
    {
        $intervals = ['5 minutes'];
        $this->createModelWithIntervals(Log::class, $intervals);

        $this->assertEquals(1, Projection::first()->content["number of logs"]);
    }

    /** @test */
    public function it_computes_the_content_of_the_projection()
    {
        $intervals = ['5 minutes'];
        $this->createModelWithIntervals(Log::class, $intervals);
        $this->createModelWithIntervals(Log::class, $intervals);

        $this->assertEquals(2, Projection::first()->content["number of logs"]);
    }

    /** @test */
    public function it_dispatch_a_job_when_the_queue_config_is_enabled()
    {
        Queue::fake();
        config(['cargo.queue' => true]);

        $this->createModelWithIntervals(Log::class, ['5 minutes']);

        Queue::assertPushed(ProcessProjection::class);
    }

    /** @test */
    public function it_dispatch_a_job_to_the_named_queue()
    {
        Queue::fake();
        config(['cargo.queue' => true, 'cargo.queue_name' => 'named']);

        $this->createModelWithIntervals(Log::class, ['5 minutes']);

        Queue::assertPushedOn('named', ProcessProjection::class);
    }

    /** @test */
    public function it_has_a_relationship_with_the_projection()
    {
        $log = Log::factory()->create();

        $this->assertNotNull($log->projections);
    }

    /**
     * Creates the model with the given intervals.
     */
    private function createModelWithIntervals(string $model, array $intervals): Model
    {
        $model = $model::factory()->make();
        $model->setInterval($intervals);
        $model->save();

        return $model;
    }
}
