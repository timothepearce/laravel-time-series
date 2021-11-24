<?php

namespace TimothePearce\Quasar\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use TimothePearce\Quasar\Quasar;

class ProjectModelsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'quasar:project {model?*}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Projects the existing models or a specific one.';

    /**
     * Create a new command instance.
     * @return void
     * @todo add warning if projection already exists.
     * @todo implements queue.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Executes the command operations.
     */
    public function handle(): void
    {
        $this->getProjectableModelNames()
            ->map(fn(string $modelName) => $modelName::all())
            ->flatten()
            ->sortBy('created_at')
            ->each
            ->bootProjectors();
    }

    /**
     * Get the provided projectable model name or guess them.
     */
    private function getProjectableModelNames(): Collection
    {
        return empty($this->argument()['model']) ?
            app(Quasar::class)->resolveProjectableModels() :
            $this->resolveModelFromArgument();
    }

    /**
     * Resolve the model
     */
    private function resolveModelFromArgument(): Collection
    {
        return collect($this->arguments()['model'])->map(
            fn(string $modelName) => config('quasar.models_namespace') . $modelName
        );
    }
}
