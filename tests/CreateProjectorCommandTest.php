<?php


namespace Laravelcargo\LaravelCargo\Tests;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;

class CreateProjectorCommandTest extends TestCase
{
    protected string $projectorClassName;

    /** @test */
    public function it_creates_a_new_projector_file()
    {
        $projectorClass = app_path('Projectors/ProjectorClass.php');

        $this->assertMissingFile($projectorClass);

        $this->createFile($projectorClass);

        $this->assertRightFileContent($projectorClass);
    }

    private function assertMissingFile(string $projectorClass)
    {
        if (File::exists($projectorClass)) {
            unlink($projectorClass);
        }

        $this->assertFalse(File::exists($projectorClass));
    }

    private function createFile(string $projectorClass)
    {
        Artisan::call('make:projector ProjectorClass');

        $this->assertTrue(File::exists($projectorClass));
    }

    private function assertRightFileContent(string $projectorClass)
    {

        // Assert the file contains the right contents
        $expectedContents = <<<CLASS
<?php

namespace App\Projectors;

use Laravelcargo\LaravelCargo\Models\Projection;
use Laravelcargo\LaravelCargo\Projector;

class ProjectorClass extends Projector
{
    /**
     * The list of time intervals.
     *
     * @var string[]
     */
    protected array \$intervals = [];

    /**
     * The default projection content.
     */
    public function defaultContent(): array
    {
        return [];
    }

    /**
     * Compute the projection.
     */
    public function handle(Projection \$projection): array
    {
        return [];
    }
}

CLASS;

        $this->assertEquals($expectedContents, file_get_contents($projectorClass));
    }
}
