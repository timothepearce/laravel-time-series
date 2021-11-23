<?php

namespace TimothePearce\Quasar\Jobs;

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
     * Create a new job instance.
     */
    public function __construct(protected Model $model)
    {
        $this->onQueue(config('quasar.queue_name'));
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $this->model->bootProjectors();
    }
}
