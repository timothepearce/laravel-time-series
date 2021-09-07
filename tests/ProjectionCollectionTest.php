<?php

namespace Laravelcargo\LaravelCargo\Tests;

use Laravelcargo\LaravelCargo\Models\Projection;
use Laravelcargo\LaravelCargo\Tests\Models\Log;
use Laravelcargo\LaravelCargo\Tests\Projectors\MultipleIntervalsProjector;
use Laravelcargo\LaravelCargo\Tests\Projectors\SingleIntervalProjector;
use Laravelcargo\LaravelCargo\Tests\Projectors\SingleIntervalProjectorWithUniqueKey;

class ProjectionCollectionTest extends TestCase
{
    use WithProjectableFactory;
}
