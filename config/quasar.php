<?php

use Carbon\CarbonInterface;

return [

    /*
     * Your models namespace.
     */
    'models_namespace' => 'App\\Models',

    /*
     * When enabled, Quasar will process the projections on a queue.
     */
    'queue' => false,

    /*
     * The specific queue name used by Quasar.
     * Leave empty to use the default queue.
     */
    'queue_name' => '',

    /*
     * The first day of the week.
     */
    'beginning_of_the_week' => CarbonInterface::MONDAY,
];
