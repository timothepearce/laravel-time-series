<?php

namespace TimothePearce\TimeSeries\Contracts;

interface ProjectionContract
{
    /**
     * The default projection content.
     */
    public function defaultContent(): array;
}
