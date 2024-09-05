<?php

namespace TimothePearce\TimeSeries\Tests;

use Illuminate\Support\Carbon;
use TimothePearce\TimeSeries\Collections\ProjectionCollection;
use TimothePearce\TimeSeries\Exceptions\MissingProjectionNameException;
use TimothePearce\TimeSeries\Exceptions\MissingProjectionPeriodException;
use TimothePearce\TimeSeries\Models\Projection;
use TimothePearce\TimeSeries\Tests\Models\TableReservation;
use TimothePearce\TimeSeries\Tests\Models\Projections\TableReservationPerMadeDayProjection;
use TimothePearce\TimeSeries\Tests\Models\Projections\TableReservationPerDiningDayProjection;
use TimothePearce\TimeSeries\Tests\Models\Projections\TableReservationPerDiningDayProjectionWithKey;

class ProjectionWithConfigurableDateTest extends TestCase
{
    use ProjectableFactory;

    public function setUp(): void
    {
        parent::setUp();

        $this->travelTo(Carbon::today());
    }

    /** @test */
    public function it_gets_a_custom_collection()
    {
        TableReservation::factory()->count(2)->create();

        $collection = Projection::all();

        $this->assertInstanceOf(ProjectionCollection::class, $collection);
    }

    /** @test */
    public function it_has_a_relationship_with_the_model()
    {
        TableReservation::factory()->create();
        $projection = Projection::first();

        $this->assertNotEmpty($projection->from(TableReservation::class)->get());
    }

    /** @test */
    public function it_gets_the_projections_from_projection_name()
    {
        $this->createModelWithProjections(TableReservation::class, [TableReservationPerDiningDayProjection::class]);
        $this->createModelWithProjections(TableReservation::class, [TableReservationPerMadeDayProjection::class]);

        $numberOfProjections = Projection::name(TableReservationPerDiningDayProjection::class)->count();

        $this->assertEquals(1, $numberOfProjections);
    }

    /** @test */
    public function it_gets_the_projections_from_a_single_period()
    {
        $this->createModelWithProjections(TableReservation::class, [TableReservationPerDiningDayProjection::class]); // 1
        $this->createModelWithProjections(TableReservation::class, [TableReservationPerDiningDayProjection::class]); // 1
        $this->travel(5)->days();
        $this->createModelWithProjections(TableReservation::class, [TableReservationPerDiningDayProjection::class]); // 2

        $numberOfProjections = Projection::period('1 day')->count();

        $this->assertEquals(2, $numberOfProjections);
    }

    /** @test */
    public function it_raises_an_exception_when_using_the_between_scope_without_a_period()
    {
        $this->expectException(MissingProjectionNameException::class);

        Projection::between(now()->subMinute(), now());
    }

    /** @test */
    public function it_raises_an_exception_when_using_the_between_scope_without_the_projection_name()
    {
        $this->expectException(MissingProjectionPeriodException::class);

        Projection::name(TableReservationPerDiningDayProjection::class)->between(now()->subMinute(), now());
    }

    /** @test */
    public function it_gets_the_projections_between_the_given_dates_for_made_date()
    {
        $this->createModelWithProjections(TableReservation::class, [TableReservationPerMadeDayProjection::class]); // 1 // Should be excluded
        $this->travel(5)->days();
        $tablereservation = $this->createModelWithProjections(TableReservation::class, [TableReservationPerMadeDayProjection::class]); // 1 // Should be included
        $this->travel(5)->days();
        $this->createModelWithProjections(TableReservation::class, [TableReservationPerMadeDayProjection::class]); // 1 // Should be excluded

        $this->travelBack();

        $betweenProjections = Projection::name(TableReservationPerMadeDayProjection::class)
            ->period('1 day')
            ->between(
                Carbon::today()->addDays(5),
                Carbon::today()->addDays(10)
            )->get();
        $this->assertCount(1, $betweenProjections);
        $this->assertEquals($betweenProjections->first()->id, $tablereservation->firstProjection(TableReservationPerMadeDayProjection::class)->id);
        $this->assertEquals($betweenProjections->first()->start_date, Carbon::today()->addDays(5));

    }

    /** @test */
    public function it_gets_the_projections_between_the_given_dates_for_dining_date()
    {
        $this->createModelWithProjections(TableReservation::class, [TableReservationPerDiningDayProjection::class]); // 1 // Should be excluded
        $this->travel(5)->days();
        $tablereservation = $this->createModelWithProjections(TableReservation::class, [TableReservationPerDiningDayProjection::class]); // 1 // Should be included
        $this->travel(5)->days();
        $this->createModelWithProjections(TableReservation::class, [TableReservationPerDiningDayProjection::class]); // 1 // Should be excluded

        $this->travelBack();

        /* Here we test based on the dining date of the reservation and it should be made_date + 10 days */
        $betweenProjections = Projection::name(TableReservationPerDiningDayProjection::class)
            ->period('1 day')
            ->between(
                Carbon::today()->addDays(15),
                Carbon::today()->addDays(20)
            )->get();

        $this->assertCount(1, $betweenProjections);
        $this->assertEquals($betweenProjections->first()->id, $tablereservation->firstProjection(TableReservationPerDiningDayProjection::class)->id);
        $this->assertEquals($betweenProjections->first()->start_date, Carbon::today()->addDays(15)); // dining_date is set 10 day from creation date
    }

    /** @test */
    public function it_gets_the_projections_between_the_given_dates_for_all_projections()
    {
        $tablereservation1 = TableReservation::factory()->create();
         // 1 made_date = created_at = now(), reservation_date = now()+10 days, total_people = 2, number_reservation = 1
        $this->travel(15)->minutes();
        $tablereservation2 = TableReservation::factory()->create();
        // 2 made_date = created_at = now(), reservation_date = now()+10, total_people = 2+2, number_reservation = 1+1
        $this->travel(2)->days();
        $tablereservation3 = TableReservation::factory()->create();
        // 3 made_date = created_at = now()+2, reservation_date = now()+2+10, total_people = 2, number_reservation = 1

        $this->travelBack(); // reset the Carbon:date back to today

        /* Here we test based on the dining date of the reservation and it should be made_date + 10 days */
        $betweenProjections = Projection::name(TableReservationPerDiningDayProjection::class)
            ->period('1 day')
            ->between(
                Carbon::today()->addDays(10),
                Carbon::today()->addDays(11)
            )->get();
        $this->assertCount(1, $betweenProjections);
        $this->assertEquals(4, $betweenProjections->first()->content['total_people']);
        $this->assertEquals(2, $betweenProjections->first()->content['number_reservations']);

        $betweenProjections = Projection::name(TableReservationPerMadeDayProjection::class)
            ->period('1 day')
            ->between(
                Carbon::today(),
                Carbon::today()->addDays(2)
            )->get();
        $this->assertCount(1, $betweenProjections);
        $this->assertEquals(4, $betweenProjections->first()->content['total_people']);
        $this->assertEquals(2, $betweenProjections->first()->content['number_reservations']);

        $betweenProjections = Projection::name(TableReservationPerDiningDayProjection::class)
            ->period('1 day')
            ->between(
                Carbon::today()->addDays(12),
                Carbon::today()->addDays(13)
            )->get();
        $this->assertCount(1, $betweenProjections);
        $this->assertEquals(2, $betweenProjections->first()->content['total_people']);
        $this->assertEquals(1, $betweenProjections->first()->content['number_reservations']);

        $betweenProjections = Projection::name(TableReservationPerMadeDayProjection::class)
            ->period('1 day')
            ->between(
                Carbon::today()->addDays(2),
                Carbon::today()->addDays(3)
            )->get();
        $this->assertCount(1, $betweenProjections);
        $this->assertEquals(2, $betweenProjections->first()->content['total_people']);
        $this->assertEquals(1, $betweenProjections->first()->content['number_reservations']);
    }
}
