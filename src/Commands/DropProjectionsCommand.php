<?php

namespace TimothePearce\Quasar\Commands;

use Illuminate\Console\Command;
use TimothePearce\Quasar\Models\Projection;

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
     * @todo ask confirmation in production
     */
    public function handle(): void
    {
        if (empty($this->argument('projection'))) {
            Projection::query()->delete();
            return;
        }
    }
}
