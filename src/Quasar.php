<?php

namespace TimothePearce\Quasar;

use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use ReflectionClass;
use TimothePearce\Quasar\Models\Traits\Projectable;

class Quasar
{
    /**
     * Resolves the projectable models from the app.
     */
    public function resolveProjectableModels(): Collection
    {
        $models = $this->getModels(app_path('Models'));

        return $models->filter(function ($model) {
            $rc = new ReflectionClass($model);
            $classes = $rc->getTraits();

            return isset($classes[Projectable::class]);
        });
    }

    /**
     * Resolves the floored date from the given period.
     */
    public function resolveFlooredDate(Carbon $date, string $period): CarbonInterface
    {
        [$quantity, $periodType] = Str::of($period)->split('/[\s]+/');

        $startDate = $date->floorUnit($periodType, $quantity);

        if (in_array($periodType, ['week', 'weeks'])) {
            $startDate->startOfWeek(config('quasar.beginning_of_the_week'));
        }

        return $startDate;
    }

    /**
     * Gets the models from the given path.
     */
    private function getModels(string $path): Collection
    {
        $results = scandir($path);
        $models = collect();

        foreach ($results as $result) {
            if ($result === '.' or $result === '..') {
                continue;
            }

            $filename = $path . '/' . $result;

            is_dir($filename) ?
                $models = $models->concat($this->getModels($filename)) :
                $models->push($this->getModelNamespace($filename));
        }

        return collect($models);
    }

    /**
     * Gets the model namespace from the given filename.
     */
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
