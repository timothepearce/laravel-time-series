<?php

namespace TimothePearce\Quasar;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use ReflectionClass;

class Quasar
{
    /**
     * Guess
     */
    public function resolveProjectableModels(): Collection
    {
        $models = $this->getModels(app_path('Models'));

        return collect($models)->filter(function ($model) {
            $rc = new ReflectionClass($model);
            $classes = $rc->getTraits();

            return isset($classes["TimothePearce\Quasar\Models\Traits\Projectable"]);
        });
    }

    private function getModels(string $path): array
    {
        $results = scandir($path);
        $models = [];

        foreach ($results as $result) {
            if ($result === '.' or $result === '..') continue;

            $filename = $path . '/' . $result;

            if (is_dir($filename)) {
                $models = array_merge($models, $this->getModels($filename));
            } else {
                $models[] = $this->getModelNamespace($filename);
            }
        }

        return $models;
    }

    private function getModelNamespace(string $filename): string
    {
        $relativePath = explode(
            base_path() . '/',
            substr($filename, 0, -4)
        )[1];

        return collect(explode('/', $relativePath))
            ->map(fn($pathSegment) => Str::ucfirst($pathSegment))
            ->join('\\');
    }
}
