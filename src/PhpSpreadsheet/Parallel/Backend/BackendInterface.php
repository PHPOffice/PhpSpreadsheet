<?php

namespace PhpOffice\PhpSpreadsheet\Parallel\Backend;

use Closure;

interface BackendInterface
{
    /**
     * Execute tasks in parallel (or sequentially for the fallback backend).
     *
     * @param list<mixed> $tasks Array of task inputs
     * @param Closure $worker Function that receives a task input and returns a result
     * @param int $maxWorkers Maximum number of concurrent workers
     *
     * @return list<mixed> Results in the same order as tasks
     */
    public function execute(array $tasks, Closure $worker, int $maxWorkers): array;

    public static function isAvailable(): bool;
}
