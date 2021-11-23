<?php

namespace TimothePearce\Quasar\Tests\Commands;

use Illuminate\Support\Facades\Artisan;
use TimothePearce\Quasar\Models\Projection;
use TimothePearce\Quasar\Tests\Models\Log;
use TimothePearce\Quasar\Tests\TestCase;

class ProjectModelsCommandTest extends TestCase
{
    /** @test */
    public function it_projects_the_models()
    {
        Log::factory()->create();
        Projection::query()->delete();
        $this->assertDatabaseCount('quasar_projections', 0);

        Artisan::call('quasar:project');

        $this->assertDatabaseCount('quasar_projections', 1);
    }
}
