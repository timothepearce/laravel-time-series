<?php

use Carbon\CarbonInterface;

return [

    /*
     * Your models namespace.
     */
    'models_namespace' => 'App\\Models\\',

    /*
     * When enabled, Cargo will process the projections on a queue.
     */
    'queue' => false,

    /*
     * This queue will be used to generate derived and responsive images.
     * Leave empty to use the default queue.
     */
    'queue_name' => '',

    /*
     * The day of the beginning of the week.
     */
    'beginning_of_the_week' => CarbonInterface::MONDAY,

    /*
     * When enabled, Cargo will delete the projections when the related model is also deleted.
     */
    // 'on_cascade_delete' => false,

    /*
     * When enabled,
     */
    // 'enable_api' => false
];
