<?php

namespace TimothePearce\Quasar\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use TimothePearce\Quasar\Models\Projection;
use TimothePearce\Quasar\Quasar;

class ProjectModelsCommand extends Command
{
    const CONFIRMATION_MESSAGE = "Existing projections will be deleted. Do you wish to continue?";

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'quasar:project {model?*} {--force}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Projects the existing models or the given ones.';

    /**
     * Create a new command instance.
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
        if (!$this->askConfirmation()) {
            return;
        }

        Projection::query()->delete();

        $this->getProjectableModels()
            ->map(fn(string $modelName) => $modelName::all())
            ->flatten()
            ->sortBy('created_at')
            ->each
            ->projectModel();

        $this->info('Projections have been refreshed!');
    }

    private function askConfirmation(): bool
    {
        if (!Projection::exists() || $this->option('force')) {
            return true;
        }

        return $this->confirm(self::CONFIRMATION_MESSAGE);
    }

    /**
     * Get the provided projectable models or guess them.
     */
    private function getProjectableModels(): Collection
    {
        return empty($this->argument('model')) ?
            app(Quasar::class)->resolveProjectableModels() :
            $this->resolveModelFromArgument();
    }

    /**
     * Resolve the model from the given argument.
     */
    private function resolveModelFromArgument(): Collection
    {
        return collect($this->argument('model'))->map(
            fn(string $modelName) => config('quasar.models_namespace') . $modelName
        );
    }
}
