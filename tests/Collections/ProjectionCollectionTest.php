<?php

namespace TimothePearce\TimeSeries\Tests\Collections;

use Illuminate\Support\Carbon;
use TimothePearce\TimeSeries\Collections\ProjectionCollection;
use TimothePearce\TimeSeries\Exceptions\EmptyProjectionCollectionException;
use TimothePearce\TimeSeries\Exceptions\MultiplePeriodsException;
use TimothePearce\TimeSeries\Exceptions\MultipleProjectionsException;
use TimothePearce\TimeSeries\Exceptions\OverlappingFillBetweenDatesException;
use TimothePearce\TimeSeries\Models\Projection;
use TimothePearce\TimeSeries\Tests\Models\Log;
use TimothePearce\TimeSeries\Tests\Models\Projections\MultiplePeriodsProjection;
use TimothePearce\TimeSeries\Tests\Models\Projections\SinglePeriodProjection;
use TimothePearce\TimeSeries\Tests\ProjectableFactory;
use TimothePearce\TimeSeries\Tests\TestCase;

class ProjectionCollectionTest extends TestCase
{
    use ProjectableFactory;

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
        Log::factory()->create(); // excluded

        $this->assertEquals(1, Projection::count());
        $unfilledProjections = Projection::name(SinglePeriodProjection::class)
            ->period('5 minutes')
            ->between($startDate, $endDate)->get();
        $this->assertCount(0, $unfilledProjections);

        $filledProjections = Projection::name(SinglePeriodProjection::class)
            ->period('5 minutes')
            ->fillBetween($startDate, $endDate);
        $this->assertCount(1, $filledProjections);

        $this->assertFalse($filledProjections->first()->exists);
    }

    /** @test */
    public function it_makes_the_missing_subsequent_period_when_filled()
    {
        $startDate = now();
        $endDate = Carbon::now()->addMinutes(10);
        Log::factory()->create();

        $unfilledProjections = Projection::name(SinglePeriodProjection::class)
            ->period('5 minutes')
            ->between($startDate, $endDate)->get();
        $this->assertCount(1, $unfilledProjections);

        $filledProjections = Projection::name(SinglePeriodProjection::class)
            ->period('5 minutes')
            ->fillBetween($startDate, $endDate);
        $this->assertCount(2, $filledProjections);

        $this->assertEquals($unfilledProjections->first()->id, $filledProjections->first()->id);
        $this->assertFalse($filledProjections->last()->exists);
    }

    /** @test */
    public function it_makes_the_missing_between_period_when_filled()
    {
        $startDate = now();
        $endDate = Carbon::now()->addMinutes(15);
        Log::factory()->create();
        $this->travel(10)->minutes();
        Log::factory()->create();

        $unfilledProjections = Projection::name(SinglePeriodProjection::class)
            ->period('5 minutes')
            ->between($startDate, $endDate)->get();
        $this->assertCount(2, $unfilledProjections);

        $filledProjections = Projection::name(SinglePeriodProjection::class)
            ->period('5 minutes')
            ->fillBetween($startDate, $endDate);
        $this->assertCount(3, $filledProjections);

        $this->assertEquals($unfilledProjections->first()->id, $filledProjections->first()->id);
        $this->assertEquals($unfilledProjections->last()->id, $filledProjections->last()->id);
        $this->assertFalse($filledProjections->get(1)->exists);
    }

    /** @test */
    public function missing_periods_are_filled_with_default_content()
    {
        $filledProjections = Projection::name(SinglePeriodProjection::class)
            ->period('5 minutes')
            ->fillBetween(now(), Carbon::now()->addMinutes(10));

        $filledProjections->each(function (Projection $filledProjection) {
            $this->assertEquals($filledProjection->content, (new SinglePeriodProjection())->defaultContent());
        });
    }

    /** @test */
    public function it_raises_an_exception_when_a_multiple_projection_name_collection_is_filled()
    {
        $this->expectException(MultipleProjectionsException::class);

        $this->createModelWithProjections(Log::class, [SinglePeriodProjection::class, MultiplePeriodsProjection::class]);

        /** @var ProjectionCollection $collection */
        $collection = Projection::all();

        $collection->fillBetween(
            now(),
            now()->addMinute(),
            SinglePeriodProjection::class,
            '5 minutes'
        );
    }

    /** @test */
    public function it_raises_an_exception_when_a_multiple_periods_collection_is_filled()
    {
        $this->expectException(MultiplePeriodsException::class);

        $this->createModelWithProjections(Log::class, [MultiplePeriodsProjection::class]);

        /** @var ProjectionCollection $collection */
        $collection = Projection::all();

        $collection->fillBetween(
            now(),
            now()->addMinute(),
            MultiplePeriodsProjection::class,
            '5 minutes'
        );
    }

    /** @test */
    public function it_raises_an_exception_if_the_collection_is_empty_while_parameters_must_be_guessed()
    {
        $this->expectException(EmptyProjectionCollectionException::class);

        /** @var ProjectionCollection $emptyProjectionCollection */
        $emptyProjectionCollection = Projection::all();

        $emptyProjectionCollection->fillBetween(now(), now()->addMinute());
    }

    /** @test */
    public function it_raises_an_exception_if_the_start_date_equals_the_end_date()
    {
        $this->expectException(OverlappingFillBetweenDatesException::class);

        /** @var ProjectionCollection $collection */
        $collection = Projection::all();

        $collection->fillBetween(now(), now(), SinglePeriodProjection::class, '5 minutes');
    }

    /** @test */
    public function it_raises_an_exception_if_the_end_date_is_before_the_start_date()
    {
        $this->expectException(OverlappingFillBetweenDatesException::class);

        /** @var ProjectionCollection $collection */
        $collection = Projection::all();

        $collection->fillBetween(now(), now()->subMinute(), SinglePeriodProjection::class, '5 minutes');
    }

    /** @test */
    public function it_guess_the_period_if_no_one_is_given_when_filled()
    {
        Log::factory()->create();

        /** @var ProjectionCollection $collection */
        $collection = Projection::all();

        $filledCollection = $collection->fillBetween(now(), now()->addMinutes(5));

        $this->assertEquals($filledCollection->last()->period, '5 minutes');
    }

    /** @test */
    public function it_guess_the_projection_name_if_no_one_is_given_when_filled()
    {
        Log::factory()->create();

        /** @var ProjectionCollection $collection */
        $collection = Projection::all();

        $filledCollection = $collection->fillBetween(now(), now()->addMinutes(5));

        $this->assertEquals($filledCollection->last()->projection_name, SinglePeriodProjection::class);
    }

    /** @test */
    public function it_fills_the_missing_period_with_the_given_callable()
    {
        Log::factory()->create();

        /** @var ProjectionCollection $collection */
        $collection = Projection::all();

        $filledCollection = $collection->fillBetween(
            now(),
            now()->addMinutes(10),
            SinglePeriodProjection::class,
            '5 minutes',
            function (Projection $lastProjection) {
                return $lastProjection->content;
            }
        );

        $this->assertEquals($filledCollection->last()->content, $filledCollection->first()->content);
    }

    /** @test */
    public function it_is_formatted_to_a_time_series()
    {
        Log::factory()->create(['created_at' => today(), 'updated_at' => today()]);

        /** @var ProjectionCollection $collection */
        $collection = Projection::all();

        $timeSeriesCollection = $collection->toTimeSeries(today(), today()->addMinutes(5));

        $this->assertEquals(Projection::first()->toSegment(), $timeSeriesCollection->first());
    }

    /** @test */
    public function it_converts_the_projections_to_segments()
    {
        Log::factory()->create();

        /** @var ProjectionCollection $projections */
        $projections = Projection::all();

        $segments = $projections->toSegments();

        $this->assertEquals(Projection::first()->toSegment(), $segments->first());
    }
}
