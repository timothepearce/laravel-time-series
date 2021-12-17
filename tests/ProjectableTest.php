<?php

namespace TimothePearce\Quasar\Tests;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Queue;
use TimothePearce\Quasar\Jobs\ProcessProjection;
use TimothePearce\Quasar\Models\Projection;
use TimothePearce\Quasar\Tests\Models\Log;
use TimothePearce\Quasar\Tests\Models\Message;
use TimothePearce\Quasar\Tests\Models\Projections\GlobalPeriodProjection;
use TimothePearce\Quasar\Tests\Models\Projections\MultiplePeriodsProjection;
use TimothePearce\Quasar\Tests\Models\Projections\SinglePeriodKeyedProjection;
use TimothePearce\Quasar\Tests\Models\Projections\SinglePeriodProjection;
use TimothePearce\Quasar\Tests\Models\Projections\SinglePeriodProjectionWithCallable;
use TimothePearce\Quasar\Tests\Models\Projections\SinglePeriodProjectionWithUniqueKey;
use TimothePearce\Quasar\Tests\Models\Projections\WeeklyPeriodProjection;

class ProjectableTest extends TestCase
{
    use ProjectableFactory;

    /** @test */
    public function it_creates_the_projection_on_model_created_event()
    {
        $this->assertDatabaseCount('quasar_projections', 0);
        $log = Log::factory()->create();

        $projection = $log->firstProjection();
        $this->assertEquals($projection->content['created_count'], 1);
    }

    /** @test */
    public function it_updates_the_projection_on_model_updating_event()
    {
        $log = Log::factory()->create();
        $projection = $log->firstProjection();
        $this->assertEquals($projection->content['updating_count'], 0);

        $log->update(['message' => 'message updating']);

        $this->assertEquals($projection->refresh()->content['updating_count'], 1);
    }

    /** @test */
    public function it_updates_the_projection_on_model_updated_event()
    {
        $log = Log::factory()->create();
        $projection = $log->firstProjection();
        $this->assertEquals($projection->content['updated_count'], 0);

        $log->update(['message' => 'message updated']);

        $this->assertEquals($projection->refresh()->content['updated_count'], 1);
    }

    /** @test */
    public function it_updates_the_projection_on_model_deleting_event()
    {
        $log = Log::factory()->create();
        $projection = $log->firstProjection();
        $this->assertEquals($projection->content['deleting_count'], 0);

        $log->delete();

        $this->assertEquals($projection->refresh()->content['deleting_count'], 1);
    }

    /** @test */
    public function it_updates_the_projection_on_model_deleted_event()
    {
        $log = Log::factory()->create();
        $projection = $log->firstProjection();
        $this->assertEquals($projection->content['deleted_count'], 0);

        $log->delete();

        $this->assertEquals($projection->refresh()->content['deleted_count'], 1);
    }

    /** @test */
    public function it_creates_a_projection_for_each_interval_when_a_model_with_projections_is_created()
    {
        $this->createModelWithProjections(Log::class, [MultiplePeriodsProjection::class]);

        $this->assertDatabaseCount('quasar_projections', 8);
    }

    /** @test */
    public function it_gets_the_existing_projection_when_the_interval_is_in_completion()
    {
        $this->travelTo(Carbon::today());
        Log::factory()->create();

        $this->travel(3)->minutes();
        Log::factory()->create();

        $this->assertDatabaseCount('quasar_projections', 1);
    }

    /** @test */
    public function it_creates_a_new_projection_when_the_interval_is_ended()
    {
        $this->travelTo(Carbon::today());
        Log::factory()->create();

        $this->travel(6)->minutes();
        Log::factory()->create();

        $this->assertDatabaseCount('quasar_projections', 2);
    }

    /** @test */
    public function it_dispatch_a_job_when_the_queue_config_is_enabled()
    {
        Queue::fake();
        config(['quasar.queue' => true]);

        Log::factory()->create();

        Queue::assertPushed(ProcessProjection::class);
    }

    /** @test */
    public function it_dispatch_a_job_to_the_named_queue()
    {
        Queue::fake();
        config(['quasar.queue' => true, 'quasar.queue_name' => 'named']);

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
    public function it_projects_the_data_with_a_start_date_equals_to_the_beginning_of_the_week()
    {
        $this->travelTo(new Carbon('2021-12-17 00:00:00')); // wednesday
        $log = $this->createModelWithProjections(Log::class, [
            WeeklyPeriodProjection::class,
        ]);

        $projection = $log->projections(WeeklyPeriodProjection::class)->first();

        $this->assertEquals(
            Carbon::createFromDate(2021, 12, 13), // monday
            $projection->start_date,
        );
    }

    /** @test */
    public function it_get_the_projections_from_a_single_type()
    {
        $log = $this->createModelWithProjections(Log::class, [
            SinglePeriodProjection::class,
            MultiplePeriodsProjection::class,
        ]);
        $projections = $log->projections(MultiplePeriodsProjection::class)->get();

        $this->assertCount(8, $projections);
        $projections->each(function (Projection $projection) {
            $this->assertEquals(MultiplePeriodsProjection::class, $projection->projection_name);
        });
    }

    /** @test */
    public function it_get_the_projections_from_a_single_type_and_period()
    {
        $log = $this->createModelWithProjections(Log::class, [
            SinglePeriodProjection::class,
            MultiplePeriodsProjection::class,
        ]);

        $projections = $log->projections(MultiplePeriodsProjection::class, '5 minutes')->get();

        $this->assertCount(1, $projections);
        $this->assertEquals('5 minutes', $projections->first()->period);
    }

    /** @test */
    public function it_get_the_projections_from_a_single_type_and_multiple_periods()
    {
        $log = $this->createModelWithProjections(Log::class, [
            SinglePeriodProjection::class,
            MultiplePeriodsProjection::class,
        ]);

        $projections = $log->projections(MultiplePeriodsProjection::class, ['5 minutes', '1 hour'])->get();

        $this->assertCount(2, $projections);
        $projections->each(function (Projection $projection) {
            $this->assertTrue(collect(['5 minutes', '1 hour'])->contains($projection->period));
        });
    }

    /** @test */
    public function it_creates_a_single_projection_for_models_with_the_same_projection()
    {
        $this->createModelWithProjections(Log::class, [SinglePeriodProjection::class]);
        $this->createModelWithProjections(Message::class, [SinglePeriodProjection::class]);

        $this->assertEquals(1, Projection::count());
    }

    /** @test */
    public function it_updates_a_projection_for_a_single_projectable_type_and_interval()
    {
        $log = $this->createModelWithProjections(Log::class, [SinglePeriodProjection::class]);
        $message = $this->createModelWithProjections(Message::class, [MultiplePeriodsProjection::class]);

        $this->createModelWithProjections(Log::class, [SinglePeriodProjection::class]);

        $logProjection = $log->projections(SinglePeriodProjection::class, '5 minutes')->first();
        $messageProjection = $message->projections(MultiplePeriodsProjection::class, '5 minutes')->first();

        $this->assertEquals(2, $logProjection->content['created_count']);
        $this->assertEquals(1, $messageProjection->content['created_count']);
    }

    /** @test */
    public function it_creates_a_global_projection()
    {
        $this->createModelWithProjections(Log::class, [GlobalPeriodProjection::class]);

        $this->assertEquals(1, Projection::count());
        $this->assertEquals(1, Projection::first()->content['created_count']);
    }

    /** @test */
    public function it_creates_a_projection_for_each_different_key()
    {
        $this->createModelWithProjections(Log::class, [SinglePeriodProjectionWithUniqueKey::class]);
        $this->createModelWithProjections(Log::class, [SinglePeriodProjectionWithUniqueKey::class]);

        $this->assertEquals(2, Projection::count());
    }

    /** @test */
    public function it_creates_a_single_projection_for_a_similar_key()
    {
        $this->createModelWithProjections(Log::class, [SinglePeriodKeyedProjection::class]);
        $this->createModelWithProjections(Log::class, [SinglePeriodKeyedProjection::class]);

        $this->assertEquals(1, Projection::count());
    }

    /** @test */
    public function it_creates_a_projection_with_the_model_name_created_hook()
    {
        $log = $this->createModelWithProjections(Log::class, [SinglePeriodProjectionWithCallable::class]);

        $logProjection = $log->projections(SinglePeriodProjectionWithCallable::class, '5 minutes')->first();

        $this->assertEquals(1, $logProjection->content['created_count']);
    }

    /** @test */
    public function it_updates_a_global_projection()
    {
        $log = $this->createModelWithProjections(Log::class, [GlobalPeriodProjection::class]);

        $log->update(['message' => 'updated message']);

        $this->assertEquals(1, Projection::count());
        $this->assertEquals(1, Projection::first()->content['updated_count']);
    }

    /** @test */
    public function it_updates_a_projection_with_the_model_name_updated_hook()
    {
        $log = $this->createModelWithProjections(Log::class, [SinglePeriodProjectionWithCallable::class]);
        $log->update(['message' => 'updated message']);

        $logProjection = $log->projections(SinglePeriodProjectionWithCallable::class, '5 minutes')->first();

        $this->assertEquals(1, $logProjection->content['updated_count']);
    }

    /** @test */
    public function it_updates_a_projection_with_the_model_name_deleted_hook()
    {
        $log = $this->createModelWithProjections(Log::class, [SinglePeriodProjectionWithCallable::class]);
        $log->delete();

        $logProjection = $log->projections(SinglePeriodProjectionWithCallable::class, '5 minutes')->first();

        $this->assertEquals(1, $logProjection->content['deleted_count']);
    }

    /** @test */
    public function it_get_the_first_projections()
    {
        $log = Log::factory()->create();

        $firstProjection = $log->firstProjection();

        $this->assertEquals($firstProjection->id, Projection::first()->id);
    }
}
