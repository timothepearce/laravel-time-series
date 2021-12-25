<?php

use Carbon\CarbonInterface;

return [

    /*
     * Your models namespace.
     */
    'models_namespace' => 'App\\Models\\',

    /*
     * When enabled, Quasar will process the projections on a queue.
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
];
