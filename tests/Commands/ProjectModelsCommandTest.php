<?php

namespace TimothePearce\TimeSeries\Tests\Commands;

use Illuminate\Support\Facades\Artisan;
use Mockery\MockInterface;
use TimothePearce\TimeSeries\Models\Projection;
use TimothePearce\TimeSeries\TimeSeries;
use TimothePearce\TimeSeries\Tests\Models\Log;
use TimothePearce\TimeSeries\Tests\Models\Message;
use TimothePearce\TimeSeries\Tests\TestCase;

class ProjectModelsCommandTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->mockResolveProjectableModels();
    }

    /** @test */
    public function it_deletes_the_previous_projections()
    {
        Log::factory()->create();
        Log::query()->delete();
        $this->assertDatabaseCount('logs', 0);
        $this->assertDatabaseCount('time_series_projections', 1);

        Artisan::call("time-series:project Log --force");

        $this->assertDatabaseCount('time_series_projections', 0);
    }

    /** @test */
    public function it_projects_the_models_when_executed()
    {
        Log::factory()->create();
        Projection::query()->delete();
        $this->assertDatabaseCount('logs', 1);
        $this->assertDatabaseCount('time_series_projections', 0);

        Artisan::call("time-series:project Log");

        $this->assertDatabaseCount('time_series_projections', 1);
    }

    /** @test */
    public function it_projects_the_trashed_models_when_executed()
    {
        Message::factory()->create();
        Projection::query()->delete();
        Message::query()->delete();

        $this->assertDatabaseCount('messages', 1);
        $this->assertDatabaseCount('time_series_projections', 0);

        Artisan::call("time-series:project Message --with-trashed");
        $this->assertDatabaseCount('time_series_projections', 1);
    }

    /**
     * Mocks the `resolveProjectableModels` methods from the TimeSeries class.
     */
    private function mockResolveProjectableModels()
    {
        $this->partialMock(
            TimeSeries::class,
            fn (MockInterface $mock) => $mock
                ->shouldReceive('resolveProjectableModels')
                ->andReturns(["Log"])
        );
    }
}
