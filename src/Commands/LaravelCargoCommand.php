<?php

namespace Laravelcargo\LaravelCargo\Commands;

use Illuminate\Console\Command;

class LaravelCargoCommand extends Command
{
    // projection:content --model="" ?
    public $signature = 'cargo';

    public $description = 'My command';

    public function handle()
    {
        $this->comment('All done');
    }
}
