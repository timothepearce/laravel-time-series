<?php

namespace TimothePearce\Quasar;

use Illuminate\Support\Collection;

class Quasar
{
    /**
     * @todo Implement the method.
     */
    public function guessProjectableModel(): Collection
    {
        // Get all the model classes by getting the config('app.model_path') or something
        // Filter the one with the "Projectable" trait
        return collect([]);
    }
}
