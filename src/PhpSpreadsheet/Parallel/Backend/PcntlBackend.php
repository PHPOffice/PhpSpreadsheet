<?php

namespace PhpOffice\PhpSpreadsheet\Parallel\Backend;

use Closure;
use PhpOffice\PhpSpreadsheet\Exception;
use Throwable;

class PcntlBackend implements BackendInterface
{
    private const DEFAULT_TIMEOUT = 60;

    private int $timeout;

    public function __construct(int $timeout = self::DEFAULT_TIMEOUT)
    {
        $this->timeout = $timeout;
    }

    public function execute(array $tasks, Closure $worker, int $maxWorkers): array
    {
        if (!self::isAvailable()) {
            throw new Exception('pcntl extension is not available'); // @codeCoverageIgnore
        }

        $taskCount = count($tasks);
        $results = array_fill(0, $taskCount, null);
        $tempFiles = [];
        $pids = [];
        $isChild = false;

        try {
            // Process tasks in batches of maxWorkers
            for ($batchStart = 0; $batchStart < $taskCount; $batchStart += $maxWorkers) {
                $batchEnd = min($batchStart + $maxWorkers, $taskCount);
                $batchPids = [];

                // Fork children for this batch
                for ($i = $batchStart; $i < $batchEnd; ++$i) {
                    $tempFile = tempnam(sys_get_temp_dir(), 'phpspreadsheet_parallel_');
                    if ($tempFile === false) {
                        throw new Exception('Failed to create temp file for parallel execution'); // @codeCoverageIgnore
                    }
                    $tempFiles[$i] = $tempFile;

                    $pid = pcntl_fork();
                    if ($pid === -1) {
                        throw new Exception('Failed to fork process'); // @codeCoverageIgnore
                    }

                    if ($pid === 0) {
                        // Child process — coverage cannot be collected from forked children
                        // @codeCoverageIgnoreStart
                        $isChild = true;

                        try {
                            $result = $worker($tasks[$i]);
                            file_put_contents($tempFile, serialize($result));
                        } catch (Throwable $e) {
                            file_put_contents($tempFile, serialize(
                                new ParallelTaskError($e->getMessage(), (int) $e->getCode())
                            ));
                        }
                        exit(0);
                        // @codeCoverageIgnoreEnd
                    }

                    // Parent process
                    $pids[$i] = $pid;
                    $batchPids[$i] = $pid;
                }

                // Wait for all children in this batch
                foreach ($batchPids as $i => $pid) {
                    $this->waitForChild($pid);
                }

                // Collect results for this batch
                foreach ($batchPids as $i => $pid) {
                    if (!isset($tempFiles[$i]) || !is_file($tempFiles[$i])) {
                        throw new Exception("Result file missing for task {$i}"); // @codeCoverageIgnore
                    }

                    $content = file_get_contents($tempFiles[$i]);
                    if ($content === false) {
                        throw new Exception("Failed to read result for task {$i}"); // @codeCoverageIgnore
                    }

                    $result = unserialize($content);
                    if ($result instanceof ParallelTaskError) {
                        throw new Exception("Parallel task {$i} failed: " . $result->getMessage());
                    }

                    $results[$i] = $result;
                }
            }
        } finally {
            // Only parent cleans up — child must not touch shared state
            if (!$isChild) {
                // Reap any remaining children
                foreach ($pids as $pid) {
                    pcntl_waitpid($pid, $status, WNOHANG);
                }

                // Clean up temp files
                foreach ($tempFiles as $file) {
                    if (is_file($file)) {
                        @unlink($file);
                    }
                }
            }
        }

        return array_values($results);
    }

    private function waitForChild(int $pid): void
    {
        $startTime = time();

        while (true) {
            $result = pcntl_waitpid($pid, $status, WNOHANG);

            if ($result === $pid) {
                return;
            }

            if ($result === -1) {
                return; // @codeCoverageIgnore
            }

            if ((time() - $startTime) >= $this->timeout) {
                // Attempt graceful termination
                if (function_exists('posix_kill')) {
                    posix_kill($pid, 15); // SIGTERM
                    usleep(100000); // 100ms grace period
                }
                pcntl_waitpid($pid, $status, WNOHANG);

                throw new Exception("Parallel task timed out after {$this->timeout} seconds");
            }

            usleep(10000); // 10ms poll interval
        }
    }

    public static function isAvailable(): bool
    {
        return function_exists('pcntl_fork')
            && function_exists('pcntl_waitpid')
            && PHP_OS_FAMILY !== 'Windows';
    }
}
