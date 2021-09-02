<?php

namespace Laravelcargo\LaravelCargo\Commands;

use Illuminate\Console\GeneratorCommand;

class CreateProjectorCommand extends GeneratorCommand
{
    public $name = 'make:projector';

    public $description = 'Create a new projector class';

    protected $type = 'Projector';

    protected function getStub()
    {
        return __DIR__ . '/stubs/Projector.php.stub';
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\Projectors';
    }

    public function handle()
    {
        parent::handle();

        $this->doOtherOperations();
    }

    protected function doOtherOperations()
    {
        $class = $this->qualifyClass($this->getNameInput());
        $path = $this->getPath($class);
        $content = file_get_contents($path);

        file_put_contents($path, $content);
    }
}
