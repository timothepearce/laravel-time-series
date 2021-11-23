<?php

return [

    /**
     * Your projections model path.
     */
    'projections_path' => 'App\\Models\\Projections\\',

    /*
     * When enabled, Cargo will process the projections on a queue.
     */
    'queue' => true,

    /*
     * This queue will be used to generate derived and responsive images.
     * Leave empty to use the default queue.
     */
    'queue_name' => '',

    /*
     * The fully qualified class name of the projection model.
     */
    // 'projection_model' => \Laravelcargo\LaravelCargo\Models\Projection::class,

    /*
     * When enabled, Cargo will delete the projections when the related model is also deleted.
     */
    // 'on_cascade_delete' => false,

    /*
     * When enabled,
     */
    // 'enable_api' => false

    /*
     * The namespace of the projector class.
     */
    // 'projectors_namespace => ''
];
