<?php

namespace TimothePearce\Quasar\Tests;

use Illuminate\Support\Carbon;
use TimothePearce\Quasar\Exceptions\MissingProjectionPeriodException;
use TimothePearce\Quasar\Exceptions\MissingProjectorNameException;
use TimothePearce\Quasar\Models\Projection;
use TimothePearce\Quasar\ProjectionCollection;
use TimothePearce\Quasar\Tests\Models\Log;
use TimothePearce\Quasar\Tests\Projectors\MultiplePeriodsProjector;
use TimothePearce\Quasar\Tests\Projectors\SinglePeriodProjector;
use TimothePearce\Quasar\Tests\Projectors\SinglePeriodProjectorWithUniqueKey;

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
    public function it_get_the_projections_from_projector_name()
    {
        $this->createModelWithProjections(Log::class, [SinglePeriodProjector::class]);
        $this->createModelWithProjections(Log::class, [MultiplePeriodsProjector::class]);

        $numberOfProjections = Projection::fromProjector(SinglePeriodProjector::class)->count();

        $this->assertEquals(1, $numberOfProjections);
    }

    /** @test */
    public function it_get_the_projections_from_a_single_period()
    {
        $this->createModelWithProjections(Log::class, [MultiplePeriodsProjector::class]); // 1
        $this->createModelWithProjections(Log::class, [MultiplePeriodsProjector::class]); // 1
        $this->travel(6)->minutes();
        $this->createModelWithProjections(Log::class, [MultiplePeriodsProjector::class]); // 2

        $numberOfProjections = Projection::period('5 minutes')->count();

        $this->assertEquals(2, $numberOfProjections);
    }

    /** @test */
    public function it_raises_an_exception_when_using_the_between_scope_without_a_period()
    {
        $this->expectException(MissingProjectorNameException::class);

        Projection::between(now()->subMinute(), now());
    }

    /** @test */
    public function it_raises_an_exception_when_using_the_between_scope_without_the_projector_name()
    {
        $this->expectException(MissingProjectionPeriodException::class);

        Projection::fromProjector(SinglePeriodProjector::class)->between(now()->subMinute(), now());
    }

    /** @test */
    public function it_get_the_projections_between_the_given_dates()
    {
        Log::factory()->create(); // Should be excluded
        $this->travel(5)->minutes();
        $log = Log::factory()->create(); // Should be included
        $this->travel(5)->minutes();
        Log::factory()->create(); // Should be excluded

        $betweenProjections = Projection::fromProjector(SinglePeriodProjector::class)
            ->period('5 minutes')
            ->between(
                Carbon::today()->addMinutes(5),
                Carbon::today()->addMinutes(10)
            )->get();

        $this->assertCount(1, $betweenProjections);
        $this->assertEquals($betweenProjections->first()->id, $log->firstProjection()->id);
        $this->assertEquals($betweenProjections->first()->start_date, Carbon::today()->addMinutes(5));
    }

    /** @test */
    public function it_rounds_to_the_floor_by_period_the_between_dates()
    {
        $log = Log::factory()->create(); // should be included
        $this->travel(5)->minutes();
        Log::factory()->create(); // should be excluded

        $betweenProjections = Projection::fromProjector(SinglePeriodProjector::class)
            ->period('5 minutes')
            ->between(
                Carbon::today()->addMinutes(4), // should be rounded to 0 minutes
                Carbon::today()->addMinutes(9) // should be rounded to 5 minutes
            )->get();

        $this->assertCount(1, $betweenProjections);
        $this->assertEquals($log->firstProjection()->id, $betweenProjections->first()->id);
    }

    /** @test */
    public function it_does_not_include_a_projection_with_a_start_date_equals_to_the_between_end_date()
    {
        $firstProjection = Log::factory()->create()->firstProjection();
        $this->travel(5)->minutes();
        $secondProjection = Log::factory()->create()->firstProjection();
        $betweenEndDate = Carbon::today()->addMinutes(5);

        $this->assertTrue(Carbon::today()->equalTo($firstProjection->start_date));
        $this->assertTrue($betweenEndDate->equalTo($secondProjection->start_date));

        $betweenProjections = Projection::fromProjector(SinglePeriodProjector::class)
            ->period('5 minutes')
            ->between(
                Carbon::today(),
                $betweenEndDate
            )->get();

        $this->assertCount(1, $betweenProjections);
        $this->assertEquals($betweenProjections->first()->id, $firstProjection->id);
    }

    /** @test */
    public function it_get_the_projection_from_a_single_key()
    {
        $log = $this->createModelWithProjections(Log::class, [SinglePeriodProjectorWithUniqueKey::class]);
        $this->createModelWithProjections(Log::class, [SinglePeriodProjectorWithUniqueKey::class]);

        $numberOfProjections = Projection::key($log->id)->count();

        $this->assertEquals(1, $numberOfProjections);
    }

    /** @test */
    public function it_get_the_projections_from_multiples_keys()
    {
        $log = $this->createModelWithProjections(Log::class, [SinglePeriodProjectorWithUniqueKey::class]);
        $anotherLog = $this->createModelWithProjections(Log::class, [SinglePeriodProjectorWithUniqueKey::class]);

        $numberOfProjections = Projection::key([$log->id, $anotherLog->id])->count();

        $this->assertEquals(2, $numberOfProjections);
    }
}
