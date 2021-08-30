<?php

namespace Laravelcargo\LaravelCargo\Commands;

use Illuminate\Console\Command;

class LaravelCargoCommand extends Command
{
    public $signature = 'laravel-cargo';

    public $description = 'My command';

    public function handle()
    {
        $this->comment('All done');
    }
}
