<?php

namespace Laravelcargo\LaravelCargo\Tests;

use Illuminate\Support\Carbon;
use Laravelcargo\LaravelCargo\Exceptions\MissingProjectionPeriodException;
use Laravelcargo\LaravelCargo\Exceptions\MultiplePeriodsException;
use Laravelcargo\LaravelCargo\Exceptions\MultiplePeriodsProjectionCollectionException;
use Laravelcargo\LaravelCargo\Models\Projection;
use Laravelcargo\LaravelCargo\ProjectionCollection;
use Laravelcargo\LaravelCargo\Tests\Models\Log;
use Laravelcargo\LaravelCargo\Tests\Projectors\MultipleIntervalsProjector;
use Laravelcargo\LaravelCargo\Tests\Projectors\SingleIntervalProjector;

class ProjectionCollectionTest extends TestCase
{
    use WithProjectableFactory;

    public function setUp(): void
    {
        parent::setUp();

        $this->travelTo(Carbon::today()->addHour());
    }

    /** @test */
    public function it_makes_the_missing_prior_period_when_filled()
    {
        $startDate = Carbon::now()->subMinutes(5);
        $endDate = now();
        Log::factory()->create();

        $unfilledProjections = Projection::fromProjector(SingleIntervalProjector::class)
              ->period('5 minutes')
              ->between($startDate, $endDate)->get();
        $this->assertCount(1, $unfilledProjections);

        $filledProjections = Projection::fromProjector(SingleIntervalProjector::class)
              ->period('5 minutes')
              ->fillBetween($startDate, $endDate);
        $this->assertCount(2, $filledProjections);

        $this->assertEquals($unfilledProjections->first()->id, $filledProjections->last()->id);
    }

    /** @test */
    public function it_makes_the_missing_subsequent_period_when_filled()
    {
        $startDate = now();
        $endDate = Carbon::now()->addMinutes(5);
        Log::factory()->create();

        $unfilledProjections = Projection::fromProjector(SingleIntervalProjector::class)
            ->period('5 minutes')
            ->between($startDate, $endDate)->get();
        $this->assertCount(1, $unfilledProjections);

        $filledProjections = Projection::fromProjector(SingleIntervalProjector::class)
            ->period('5 minutes')
            ->fillBetween($startDate, $endDate);
        $this->assertCount(2, $filledProjections);

        $this->assertEquals($unfilledProjections->first()->id, $filledProjections->first()->id);
    }

    /** @test */
    public function it_makes_the_missing_between_period_when_filled()
    {
        $startDate = now();
        $endDate = Carbon::now()->addMinutes(10);
        Log::factory()->create();
        $this->travel(10)->minutes();
        Log::factory()->create();

        $unfilledProjections = Projection::fromProjector(SingleIntervalProjector::class)
            ->period('5 minutes')
            ->between($startDate, $endDate)->get();
        $this->assertCount(2, $unfilledProjections);

        $filledProjections = Projection::fromProjector(SingleIntervalProjector::class)
            ->period('5 minutes')
            ->fillBetween($startDate, $endDate);
        $this->assertCount(3, $filledProjections);

        $this->assertEquals($unfilledProjections->first()->id, $filledProjections->first()->id);
        $this->assertEquals($unfilledProjections->last()->id, $filledProjections->last()->id);
    }

    /** @test */
    public function missing_periods_are_filled_with_default_content()
    {
        $filledProjections = Projection::fromProjector(SingleIntervalProjector::class)
            ->period('5 minutes')
            ->fillBetween(now(), Carbon::now()->addMinutes(10));

        $filledProjections->each(function (Projection $filledProjection) {
            $this->assertEquals($filledProjection->content, SingleIntervalProjector::defaultContent());
        });
    }

//    /** @test */
//    public function it_raises_an_exception_when_a_multiple_periods_collection_is_filled()
//    {
//        $this->expectException(MultiplePeriodsException::class);
//
//        $this->createModelWithProjectors(Log::class, [MultipleIntervalsProjector::class]);
//
//        /** @var ProjectionCollection $collection */
//        $collection = Projection::all();
//
//        $collection->fillBetween(
//            now(),
//            now()->addMinute(),
//            MultipleIntervalsProjector::class,
//            '5 minutes'
//        );
//    }
//
//    /** @test */
//    public function it_raises_an_exception_if_the_start_date_equals_the_end_date()
//    {
//        // @todo
//    }
//
//    /** @test */
//    public function it_raises_an_exception_if_the_end_date_is_before_the_start_date()
//    {
//        // @todo
//    }
//
//    /** @test */
//    public function it_raises_an_exception_when_a_multiple_projection_name_collection_is_filled()
//    {
//        // @todo
//    }
//
//    /** @test */
//    public function it_guess_the_period_if_no_one_is_given_when_filled()
//    {
//        // @todo
//    }
//
//    /** @test */
//    public function it_guess_the_projector_name_if_no_one_is_given_when_filled()
//    {
//        // @todo
//    }
}
