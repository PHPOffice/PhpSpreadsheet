<?php

namespace PhpOffice\PhpSpreadsheet\Parallel\Backend;

use Closure;

class SequentialBackend implements BackendInterface
{
    public function execute(array $tasks, Closure $worker, int $maxWorkers): array
    {
        $results = [];
        foreach ($tasks as $task) {
            $results[] = $worker($task);
        }

        return $results;
    }

    public static function isAvailable(): bool
    {
        return true;
    }
}
