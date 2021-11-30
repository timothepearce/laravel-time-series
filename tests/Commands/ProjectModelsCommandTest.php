<?php

namespace TimothePearce\Quasar\Tests\Commands;

use Illuminate\Support\Facades\Artisan;
use Mockery\MockInterface;
use TimothePearce\Quasar\Models\Projection;
use TimothePearce\Quasar\Quasar;
use TimothePearce\Quasar\Tests\Models\Log;
use TimothePearce\Quasar\Tests\TestCase;

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
        $this->assertDatabaseCount('quasar_projections', 1);

        Artisan::call("quasar:project Log --force");

        $this->assertDatabaseCount('quasar_projections', 0);
    }

    /** @test */
    public function it_projects_the_models_when_executed()
    {
        Log::factory()->create();
        Projection::query()->delete();
        $this->assertDatabaseCount('logs', 1);
        $this->assertDatabaseCount('quasar_projections', 0);

        Artisan::call("quasar:project Log");

        $this->assertDatabaseCount('quasar_projections', 1);
    }

    /**
     * Mocks the `resolveProjectableModels` methods from the Quasar class.
     */
    private function mockResolveProjectableModels()
    {
        $this->partialMock(
            Quasar::class,
            fn (MockInterface $mock) => $mock
                ->shouldReceive('resolveProjectableModels')
                ->andReturns(["Log"])
        );
    }
}
