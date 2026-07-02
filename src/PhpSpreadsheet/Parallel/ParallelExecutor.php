<?php

namespace PhpOffice\PhpSpreadsheet\Parallel;

use Closure;
use PhpOffice\PhpSpreadsheet\Parallel\Backend\BackendInterface;
use PhpOffice\PhpSpreadsheet\Parallel\Backend\PcntlBackend;
use PhpOffice\PhpSpreadsheet\Parallel\Backend\SequentialBackend;

class ParallelExecutor
{
    private const MAX_WORKERS_CAP = 8;

    private BackendInterface $backend;

    private ?int $maxWorkers;

    public function __construct(?BackendInterface $backend = null, ?int $maxWorkers = null)
    {
        $this->backend = $backend ?? self::detectBackend();
        $this->maxWorkers = $maxWorkers;
    }

    /**
     * Execute tasks in parallel, returning results in the same order as inputs.
     *
     * @param list<mixed> $tasks
     * @param Closure $worker Function receiving a single task, returning a result
     *
     * @return list<mixed>
     */
    public function map(array $tasks, Closure $worker): array
    {
        $taskCount = count($tasks);

        if ($taskCount <= 1) {
            return $this->executeSequential($tasks, $worker);
        }

        $workerCount = $this->resolveWorkerCount($taskCount);

        if ($workerCount < 2) {
            return $this->executeSequential($tasks, $worker);
        }

        return $this->backend->execute($tasks, $worker, $workerCount);
    }

    /**
     * @param list<mixed> $tasks
     *
     * @return list<mixed>
     */
    private function executeSequential(array $tasks, Closure $worker): array
    {
        $sequential = new SequentialBackend();

        return $sequential->execute($tasks, $worker, 1);
    }

    private function resolveWorkerCount(int $taskCount): int
    {
        if ($this->maxWorkers !== null) {
            return min($this->maxWorkers, $taskCount);
        }

        // Auto-detect: min(cpuCount - 1, taskCount, cap)
        $cpuCount = CpuDetector::detectCpuCount();
        $available = max(1, $cpuCount - 1);

        $workerCount = min($available, $taskCount, self::MAX_WORKERS_CAP);

        // Memory safety check for pcntl_fork
        if ($this->backend instanceof PcntlBackend) {
            $workerCount = $this->applyMemoryLimit($workerCount);
        }

        return $workerCount;
    }

    private function applyMemoryLimit(int $workerCount): int
    {
        $limit = self::getMemoryLimitBytes();
        if ($limit <= 0) {
            return $workerCount; // No limit set
        }

        $currentUsage = memory_get_usage(true);
        // Estimate ~30% of current usage per forked child as dirty page overhead
        $estimatedPerChild = (int) ($currentUsage * 0.3);

        if ($estimatedPerChild <= 0) {
            return $workerCount; // @codeCoverageIgnore
        }

        $headroom = $limit - $currentUsage;
        $maxSafe = (int) ($headroom / $estimatedPerChild);

        return min($workerCount, max(1, $maxSafe));
    }

    /**
     * Parse memory_limit INI value into bytes.
     *
     * @internal
     */
    public static function getMemoryLimitBytes(): int
    {
        $limit = ini_get('memory_limit');
        if ($limit === '' || $limit === '-1') {
            return 0; // No limit
        }

        $value = (int) $limit;
        $unit = strtolower(substr($limit, -1));

        switch ($unit) {
            case 'g':
                $value *= 1024;
                // no break
            case 'm':
                $value *= 1024;
                // no break
            case 'k':
                $value *= 1024;
        }

        return $value;
    }

    private static function detectBackend(): BackendInterface
    {
        if (PcntlBackend::isAvailable()) {
            return new PcntlBackend();
        }

        return new SequentialBackend(); // @codeCoverageIgnore
    }
}
