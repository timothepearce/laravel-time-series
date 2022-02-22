<?php

namespace TimothePearce\TimeSeries\Tests\Commands;

use Illuminate\Support\Facades\Artisan;
use Mockery\MockInterface;
use TimothePearce\TimeSeries\Tests\Models\Log;
use TimothePearce\TimeSeries\Tests\Models\Projections\SinglePeriodKeyedProjection;
use TimothePearce\TimeSeries\Tests\Models\Projections\SinglePeriodProjection;
use TimothePearce\TimeSeries\Tests\ProjectableFactory;
use TimothePearce\TimeSeries\Tests\TestCase;
use TimothePearce\TimeSeries\TimeSeries;

class DropProjectionsCommandTest extends TestCase
{
    use ProjectableFactory;

    /** @test */
    public function it_drops_all_the_projections()
    {
        $this->createModelWithProjections(Log::class, [
            SinglePeriodProjection::class,
            SinglePeriodKeyedProjection::class,
        ]);
        $this->assertDatabaseCount('time_series_projections', 2);

        Artisan::call("time-series:drop");

        $this->assertDatabaseCount('time_series_projections', 0);
    }

    /** @test */
    public function it_drops_the_given_projection()
    {
        $this->mockResolveProjectableModels(SinglePeriodProjection::class);
        $this->createModelWithProjections(Log::class, [
            SinglePeriodProjection::class,
            SinglePeriodKeyedProjection::class,
        ]);
        $this->assertDatabaseCount('time_series_projections', 2);

        Artisan::call('time-series:drop SinglePeriodProjection');

        $this->assertDatabaseCount('time_series_projections', 1);
    }

    /**
     * Mocks the `resolveProjectableModels` methods from the TimeSeries class.
     */
    private function mockResolveProjectableModels(string $projectionModel)
    {
        $this->partialMock(
            TimeSeries::class,
            fn (MockInterface $mock) => $mock
                ->shouldReceive('resolveProjectionModel')
                ->andReturns($projectionModel)
        );
    }
}
