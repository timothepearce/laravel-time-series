<?php

namespace TimothePearce\Quasar\Tests\Commands;

use Illuminate\Support\Facades\Artisan;
use Mockery\MockInterface;
use TimothePearce\Quasar\Quasar;
use TimothePearce\Quasar\Tests\Models\Log;
use TimothePearce\Quasar\Tests\Models\Projections\SinglePeriodKeyedProjection;
use TimothePearce\Quasar\Tests\Models\Projections\SinglePeriodProjection;
use TimothePearce\Quasar\Tests\ProjectableFactory;
use TimothePearce\Quasar\Tests\TestCase;

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
        $this->assertDatabaseCount('quasar_projections', 2);

        Artisan::call("quasar:drop");

        $this->assertDatabaseCount('quasar_projections', 0);
    }

    /** @test */
    public function it_drops_the_given_projection()
    {
        $this->mockResolveProjectableModels(SinglePeriodProjection::class);
        $this->createModelWithProjections(Log::class, [
            SinglePeriodProjection::class,
            SinglePeriodKeyedProjection::class,
        ]);
        $this->assertDatabaseCount('quasar_projections', 2);

        Artisan::call('quasar:drop SinglePeriodProjection');

        $this->assertDatabaseCount('quasar_projections', 1);
    }


    /**
     * Mocks the `resolveProjectableModels` methods from the Quasar class.
     */
    private function mockResolveProjectableModels(string $projectionModel)
    {
        $this->partialMock(
            Quasar::class,
            fn(MockInterface $mock) => $mock
                ->shouldReceive('resolveProjectionModel')
                ->andReturns($projectionModel)
        );
    }
}
