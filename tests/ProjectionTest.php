<?php


namespace Laravelcargo\LaravelCargo\Tests;

use Laravelcargo\LaravelCargo\Tests\Models\Log;
use Laravelcargo\LaravelCargo\Models\Projection;

class ProjectionTest extends TestCase
{
    /** @test */
    public function it_has_a_relationship_with_the_model()
    {
        Log::factory()->create();
        $projection = Projection::first();

        $this->assertNotNull($projection->from(Log::class)->get());
    }
}
