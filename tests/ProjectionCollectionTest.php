<?php

namespace Laravelcargo\LaravelCargo\Tests;

use Illuminate\Support\Carbon;
use Laravelcargo\LaravelCargo\Models\Projection;
use Laravelcargo\LaravelCargo\Tests\Models\Log;
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
        $startDate = Carbon::now()->subMinute();
        $endDate = now();
        Log::factory()->create();

        $unfilledProjections = Projection::name(SingleIntervalProjector::class)
              ->period('5 minutes')
              ->between($startDate, $endDate)->get();
        $this->assertCount(1, $unfilledProjections);

        $filledProjections = Projection::name(SingleIntervalProjector::class)
              ->period('5 minutes')
              ->fillBetween($startDate, $endDate)->get();
        $this->assertCount(2, $filledProjections);

        $this->assertEquals($unfilledProjections->first()->id, $filledProjections()->last()->id);
    }
//
//    /** @test */
//    public function it_makes_the_missing_subsequent_period_when_filled()
//    {
//        Log::factory()->create();
//
//        $projections = Projection::period('5 minutes')
//            ->between(Carbon::now()->addMinutes(6), Carbon::now())
//            ->filled();
//
//        $this->assertCount(1, $projectionsDB = Projection::all());
//        $this->assertCount(2, $projections);
//        $this->assertEquals($projectionsDB->first()->id, $projections()->first()->id);
//    }
//
//    /** @test */
//    public function it_makes_the_missing_between_period_when_filled()
//    {
//        Log::factory()->create();
//
//        $projections = Projection::period('5 minutes')
//            ->between(Carbon::now()->addMinutes(11), Carbon::now())
//            ->filled();
//
//        $this->assertCount(1, $projectionsDB = Projection::all());
//        $this->assertCount(2, $projections);
//        $this->assertEquals($projectionsDB->first()->id, $projections()->first()->id);
//    }
//
//    /** @test */
//    public function missing_periods_are_filled_with_default_content()
//    {
//        $projections = Projection::period('5 minutes')
//            ->between(Carbon::now()->subMinute(), Carbon::now())
//            ->filled();
//
//        $this->assertCount(1, $projectionsDB = Projection::all());
//        $this->assertCount(2, $projections);
//    }
//
//    /** @test */
//    public function it_raises_an_exception_when_a_multiple_periods_collection_is_filled()
//    {
//        // @todo
//    }
//
//    /** @test */
//    public function it_raises_an_exception_when_a_multiple_name_collection_is_filled()
//    {
//        // @todo
//    }
}
