<?php

namespace Laravelcargo\LaravelCargo\Tests;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Queue;
use Laravelcargo\LaravelCargo\Jobs\ProcessProjection;
use Laravelcargo\LaravelCargo\Models\Projection;
use Laravelcargo\LaravelCargo\Tests\Models\Log;
use Laravelcargo\LaravelCargo\Tests\Projectors\MultipleIntervalsProjector;
use Laravelcargo\LaravelCargo\Tests\Projectors\SingleIntervalProjector;

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

    /** @test */
    public function it_get_the_projections_from_a_single_type()
    {
        $log = $this->createModelWithProjectors(Log::class, [
            SingleIntervalProjector::class,
            MultipleIntervalsProjector::class,
        ]);
        $projections = $log->projections(MultipleIntervalsProjector::class)->get();

        $this->assertCount(8, $projections);
        $projections->each(function (Projection $projection) {
            $this->assertEquals(MultipleIntervalsProjector::class, $projection->name);
        });
    }

    /** @test */
    public function it_get_the_projections_from_a_single_type_and_period()
    {
        $log = $this->createModelWithProjectors(Log::class, [
            SingleIntervalProjector::class,
            MultipleIntervalsProjector::class,
        ]);

        $projections = $log->projections(MultipleIntervalsProjector::class, '5 minutes')->get();

        $this->assertCount(1, $projections);
        $this->assertEquals('5 minutes', $projections->first()->period);
    }

    /** @test */
    public function it_get_the_projections_from_a_single_type_and_multiple_periods()
    {
        $log = $this->createModelWithProjectors(Log::class, [
            SingleIntervalProjector::class,
            MultipleIntervalsProjector::class,
        ]);

        $projections = $log->projections(MultipleIntervalsProjector::class, ['5 minutes', '1 hour'])->get();

        $this->assertCount(2, $projections);
        $projections->each(function (Projection $projection) {
            $this->assertTrue(collect(['5 minutes', '1 hour'])->contains($projection->period));
        });
    }

    /** @test */
    public function it_get_the_projections_from_key()
    {
        $log->projections()
            ->period(['5 minutes', '1 hour'])
            ->key('my key')
            ->get();

        // $log->projections()->key('my key');
    }
}
