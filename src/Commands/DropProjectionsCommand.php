<?php

namespace TimothePearce\Quasar\Commands;

use Illuminate\Console\Command;
use TimothePearce\Quasar\Models\Projection;
use TimothePearce\Quasar\Quasar;

class DropProjectionsCommand extends Command
{
    /**
     * The name of the console command.
     *
     * @var string
     */
    public $name = 'quasar:drop';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'quasar:drop {projection?*} {--force}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Drop the given projections.';

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
        if (! $this->askConfirmation()) {
            return;
        }

        if (empty($this->argument('projection'))) {
            Projection::query()->delete();

            return;
        }

        collect($this->argument('projection'))->each(function (string $projectionName) {
            $projection = app(Quasar::class)->resolveProjectionModel($projectionName);

            Projection::name($projection)->delete();
        });

        $this->info('The projections have been dropped!');
    }

    private function askConfirmation()
    {
        if (config('app.env') === 'production' && ! $this->option('force')) {
            return $this->confirm("Projections will be deleted. Do you wish to continue?");
        }

        return true;
    }
}
