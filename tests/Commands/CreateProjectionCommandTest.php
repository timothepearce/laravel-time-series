<?php

namespace TimothePearce\Quasar\Tests\Commands;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use TimothePearce\Quasar\Tests\TestCase;

class CreateProjectionCommandTest extends TestCase
{
    protected string $projectionClassName;

    /** @test */
    public function it_creates_a_new_projection_file()
    {
        $projectionClass = app_path('Models/Projections/ProjectionClass.php');

        $this->assertMissingFile($projectionClass);

        $this->createProjectionFile($projectionClass);

        $this->assertProjectorFileContent($projectionClass);
    }

    /** @test */
    public function it_creates_a_new_projector_with_key_file()
    {
        $projectionClass = app_path('Models/Projections/ProjectionClass.php');

        $this->assertMissingFile($projectionClass);

        $this->createKeyedProjectionFile($projectionClass);

        $this->assertKeyedProjectionFileContent($projectionClass);
    }

    private function assertMissingFile(string $projectionClass)
    {
        if (File::exists($projectionClass)) {
            unlink($projectionClass);
        }

        $this->assertFalse(File::exists($projectionClass));
    }

    private function createProjectionFile(string $projectionClass)
    {
        Artisan::call('make:projection ProjectionClass');

        $this->assertTrue(File::exists($projectionClass));
    }

    private function createKeyedProjectionFile(string $projectorClass)
    {
        Artisan::call('make:projection ProjectionClass --key');

        $this->assertTrue(File::exists($projectorClass));
    }

    private function assertProjectorFileContent(string $projectorClass)
    {

        // Assert the file contains the right contents
        $expectedContents = <<<CLASS
<?php

namespace App\Models\Projections;

use Illuminate\Database\Eloquent\Model;
use TimothePearce\Quasar\Contracts\ProjectionContract;
use TimothePearce\Quasar\Models\Projection;

class ProjectionClass extends Projection implements ProjectionContract
{
    /**
     * Lists the available periods.
     *
     * @var string[]
     */
    public static array \$periods = [];

    /**
     * The default projection content.
     */
    public static function defaultContent(): array
    {
        return [];
    }

    /**
     * Compute the projection.
     */
    public static function projectableCreated(array \$content, Model \$model): array
    {
        return [];
    }
}

CLASS;

        $this->assertEquals($expectedContents, file_get_contents($projectorClass));
    }

    private function assertKeyedProjectionFileContent(string $projectorClass)
    {

        // Assert the file contains the right contents
        $expectedContents = <<<CLASS
<?php

namespace App\Models\Projections;

use Illuminate\Database\Eloquent\Model;
use TimothePearce\Quasar\Contracts\ProjectionContract;
use TimothePearce\Quasar\Models\Projection;

class ProjectionClass extends Projection implements ProjectionContract
{
    /**
     * Lists the available periods.
     *
     * @var string[]
     */
    public static array \$periods = [];

    /**
     * The default projection content.
     */
    public static function defaultContent(): array
    {
        return [];
    }

    /**
     * The key used to query the projection.
     */
    public static function key(Model \$model): string
    {
        return \$model->id;
    }

    /**
     * Compute the projection.
     */
    public static function projectableCreated(array \$content, Model \$model): array
    {
        return [];
    }
}

CLASS;

        $this->assertEquals($expectedContents, file_get_contents($projectorClass));
    }
}
