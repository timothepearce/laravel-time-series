<?php

namespace TimothePearce\Quasar\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;

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
     * @todo mock the guessProjectableModelNames in tests.
     * @todo add warning if projection already exists.
     * @todo add flag to project only one model.
     * @todo implements queue.
     *
     * @return void
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
        $this->getProjectableModelNames()->map(fn (string $modelName) => $modelName::all())
            ->flatten()
            ->sortBy('created_at')
            ->each->bootProjectors();
    }

    /**
     * Get the provided projectable model name or guess them.
     */
    private function getProjectableModelNames(): Collection
    {
        return is_null($this->argument()['model']) ?
            $this->guessProjectableModelNames() :
            collect($this->arguments()['model']);
    }

    /**
     * @todo Implement the method.
     */
    private function guessProjectableModelNames(): Collection
    {
        return collect([
            "TimothePearce\\Quasar\\Tests\\Models\\Log",
            "TimothePearce\\Quasar\\Tests\\Models\\Message",
        ]);
    }
}
