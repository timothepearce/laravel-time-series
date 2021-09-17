<?php

namespace Laravelcargo\LaravelCargo\Tests;

use Illuminate\Support\Carbon;
use Laravelcargo\LaravelCargo\Exceptions\EmptyProjectionCollectionException;
use Laravelcargo\LaravelCargo\Exceptions\MultiplePeriodsException;
use Laravelcargo\LaravelCargo\Exceptions\MultipleProjectorsException;
use Laravelcargo\LaravelCargo\Exceptions\OverlappingFillBetweenDatesException;
use Laravelcargo\LaravelCargo\Models\Projection;
use Laravelcargo\LaravelCargo\ProjectionCollection;
use Laravelcargo\LaravelCargo\Tests\Models\Log;
use Laravelcargo\LaravelCargo\Tests\Projectors\MultiplePeriodsProjector;
use Laravelcargo\LaravelCargo\Tests\Projectors\SinglePeriodProjector;

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
        Log::factory()->create(); // excluded

        $this->assertEquals(1, Projection::count());
        $unfilledProjections = Projection::fromProjector(SinglePeriodProjector::class)
              ->period('5 minutes')
              ->between($startDate, $endDate)->get();
        $this->assertCount(0, $unfilledProjections);

        $filledProjections = Projection::fromProjector(SinglePeriodProjector::class)
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

        $unfilledProjections = Projection::fromProjector(SinglePeriodProjector::class)
            ->period('5 minutes')
            ->between($startDate, $endDate)->get();
        $this->assertCount(1, $unfilledProjections);

        $filledProjections = Projection::fromProjector(SinglePeriodProjector::class)
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

        $unfilledProjections = Projection::fromProjector(SinglePeriodProjector::class)
            ->period('5 minutes')
            ->between($startDate, $endDate)->get();
        $this->assertCount(2, $unfilledProjections);

        $filledProjections = Projection::fromProjector(SinglePeriodProjector::class)
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
        $filledProjections = Projection::fromProjector(SinglePeriodProjector::class)
            ->period('5 minutes')
            ->fillBetween(now(), Carbon::now()->addMinutes(10));

        $filledProjections->each(function (Projection $filledProjection) {
            $this->assertEquals($filledProjection->content, SinglePeriodProjector::defaultContent());
        });
    }

    /** @test */
    public function it_raises_an_exception_when_a_multiple_projection_name_collection_is_filled()
    {
        $this->expectException(MultipleProjectorsException::class);

        $this->createModelWithProjectors(Log::class, [SinglePeriodProjector::class, MultiplePeriodsProjector::class]);

        /** @var ProjectionCollection $collection */
        $collection = Projection::all();

        $collection->fillBetween(
            now(),
            now()->addMinute(),
            SinglePeriodProjector::class,
            '5 minutes'
        );
    }

    /** @test */
    public function it_raises_an_exception_when_a_multiple_periods_collection_is_filled()
    {
        $this->expectException(MultiplePeriodsException::class);

        $this->createModelWithProjectors(Log::class, [MultiplePeriodsProjector::class]);

        /** @var ProjectionCollection $collection */
        $collection = Projection::all();

        $collection->fillBetween(
            now(),
            now()->addMinute(),
            MultiplePeriodsProjector::class,
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

        $collection->fillBetween(now(), now(), SinglePeriodProjector::class, '5 minutes');
    }

    /** @test */
    public function it_raises_an_exception_if_the_end_date_is_before_the_start_date()
    {
        $this->expectException(OverlappingFillBetweenDatesException::class);

        /** @var ProjectionCollection $collection */
        $collection = Projection::all();

        $collection->fillBetween(now(), now()->subMinute(), SinglePeriodProjector::class, '5 minutes');
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
    public function it_guess_the_projector_name_if_no_one_is_given_when_filled()
    {
        Log::factory()->create();

        /** @var ProjectionCollection $collection */
        $collection = Projection::all();

        $filledCollection = $collection->fillBetween(now(), now()->addMinutes(5));

        $this->assertEquals($filledCollection->last()->projector_name, SinglePeriodProjector::class);
    }
}
