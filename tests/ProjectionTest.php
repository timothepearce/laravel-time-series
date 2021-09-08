<?php

namespace Laravelcargo\LaravelCargo\Tests;

use Illuminate\Support\Carbon;
use Laravelcargo\LaravelCargo\Exceptions\MissingProjectionNameException;
use Laravelcargo\LaravelCargo\Exceptions\MissingProjectionPeriodException;
use Laravelcargo\LaravelCargo\Models\Projection;
use Laravelcargo\LaravelCargo\ProjectionCollection;
use Laravelcargo\LaravelCargo\Tests\Models\Log;
use Laravelcargo\LaravelCargo\Tests\Projectors\MultipleIntervalsProjector;
use Laravelcargo\LaravelCargo\Tests\Projectors\SingleIntervalProjector;
use Laravelcargo\LaravelCargo\Tests\Projectors\SingleIntervalProjectorWithUniqueKey;

class ProjectionTest extends TestCase
{
    use WithProjectableFactory;

    public function setUp(): void
    {
        parent::setUp();

        $this->travelTo(Carbon::today());
    }

    /** @test */
    public function it_get_a_custom_collection()
    {
        Log::factory()->count(2)->create();

        $collection = Projection::all();

        $this->assertInstanceOf(ProjectionCollection::class, $collection);
    }

    /** @test */
    public function it_has_a_relationship_with_the_model()
    {
        Log::factory()->create();
        $projection = Projection::first();

        $this->assertNotEmpty($projection->from(Log::class)->get());
    }

    /** @test */
    public function it_get_the_projections_from_name()
    {
        $this->createModelWithProjectors(Log::class, [SingleIntervalProjector::class]);
        $this->createModelWithProjectors(Log::class, [MultipleIntervalsProjector::class]);

        $numberOfProjections = Projection::name(SingleIntervalProjector::class)->count();

        $this->assertEquals(1, $numberOfProjections);
    }

    /** @test */
    public function it_get_the_projections_from_a_single_period()
    {
        $this->createModelWithProjectors(Log::class, [MultipleIntervalsProjector::class]); // 1
        $this->createModelWithProjectors(Log::class, [MultipleIntervalsProjector::class]); // 1
        $this->travel(6)->minutes();
        $this->createModelWithProjectors(Log::class, [MultipleIntervalsProjector::class]); // 2

        $numberOfProjections = Projection::period('5 minutes')->count();

        $this->assertEquals(2, $numberOfProjections);
    }

    /** @test */
    public function it_raises_an_exception_when_using_the_between_scope_without_a_period()
    {
        $this->expectException(MissingProjectionNameException::class);

        Projection::between(now()->subMinute(), now());
    }

    /** @test */
    public function it_raises_an_exception_when_using_the_between_scope_without_a_name()
    {
        $this->expectException(MissingProjectionPeriodException::class);

        Projection::name(SingleIntervalProjector::class)->between(now()->subMinute(), now());
    }

    /** @test */
    public function it_get_the_projections_between_the_given_dates()
    {
        Log::factory()->create();  // Should be excluded
        $this->travel(5)->minutes();
        Log::factory()->create();
        $this->travel(5)->minutes();
        Log::factory()->create();
        $this->travel(5)->minutes();
        Log::factory()->create(); // Should be excluded

        $betweenProjections = Projection::name(SingleIntervalProjector::class)
            ->period('5 minutes')
            ->between(
                Carbon::today()->addMinutes(6), // date will be rounded to the floor to 5 minutes
                Carbon::today()->addMinutes(14) // date will be rounded to the floor to 10 minutes
            )->get();

        $this->assertEquals(2, $betweenProjections->count());
        $this->assertEquals($betweenProjections->first()->start_date, Carbon::today()->addMinutes(5));
        $this->assertEquals($betweenProjections->last()->start_date, Carbon::today()->addMinutes(10));
    }

    /** @test */
    public function it_get_the_projection_from_a_single_key()
    {
        $log = $this->createModelWithProjectors(Log::class, [SingleIntervalProjectorWithUniqueKey::class]);
        $this->createModelWithProjectors(Log::class, [SingleIntervalProjectorWithUniqueKey::class]);

        $numberOfProjections = Projection::key($log->id)->count();

        $this->assertEquals(1, $numberOfProjections);
    }

    /** @test */
    public function it_get_the_projections_from_multiples_keys()
    {
        $log = $this->createModelWithProjectors(Log::class, [SingleIntervalProjectorWithUniqueKey::class]);
        $anotherLog = $this->createModelWithProjectors(Log::class, [SingleIntervalProjectorWithUniqueKey::class]);

        $numberOfProjections = Projection::key([$log->id, $anotherLog->id])->count();

        $this->assertEquals(2, $numberOfProjections);
    }
}
