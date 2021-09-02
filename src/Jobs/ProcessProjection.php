<?php

namespace Laravelcargo\LaravelCargo\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessProjection implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * The model instance
     */
    protected Model $model;

    /**
     * Create a new job instance.
     */
    public function __construct(Model $model)
    {
        $this->onQueue(config('cargo.queue_name'));

        $this->model = $model;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $this->model->bootProjectors();
    }
}
