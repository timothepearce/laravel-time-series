<?php

namespace TimothePearce\Quasar\Contracts;

interface ProjectionContract
{
    /**
     * The default projection content.
     */
    public function defaultContent(): array;
}
