<?php

namespace TimothePearce\TimeSeries\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ComputeProjection implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(protected Model $model, protected string $eventName)
    {
        $this->onQueue(config('time-series.queue_name'));
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $this->model->bootProjectors($this->eventName);
    }
}
