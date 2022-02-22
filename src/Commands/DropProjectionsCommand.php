<?php

namespace TimothePearce\TimeSeries\Commands;

use Illuminate\Console\Command;
use TimothePearce\TimeSeries\Models\Projection;
use TimothePearce\TimeSeries\TimeSeries;

class DropProjectionsCommand extends Command
{
    /**
     * The name of the console command.
     *
     * @var string
     */
    public $name = 'time-series:drop';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'time-series:drop {projection?*} {--force}';

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
            $projection = app(TimeSeries::class)->resolveProjectionModel($projectionName);

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
