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
    protected $signature = 'quasar:project';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Projects the existing models or a specific one.';

    /**
     * Create a new command instance.
     * @todo add flag to project only one model
     * @todo implements queue
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
    public function handle()
    {
        $this->guessProjectableModelNames()->map(fn (string $modelName) => $modelName::all())
            ->flatten()
            ->sortBy('created_at')
            ->each->bootProjectors();
    }

    /**
     * @todo Implement the method.
     */
    public function guessProjectableModelNames(): Collection
    {
        return collect([
            "TimothePearce\\Quasar\\Tests\\Models\\Log"
        ]);
    }
}
