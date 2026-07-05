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
                            self::writeChildResult($tempFile, ['ok' => true, 'result' => $result]);
                        } catch (Throwable $e) {
                            self::writeChildResult($tempFile, ['ok' => false, 'error' => ParallelTaskError::fromThrowable($e)]);
                        }
                        exit(0);
                        // @codeCoverageIgnoreEnd
                    }

                    // Parent process
                    $pids[$i] = $pid;
                    $batchPids[$i] = $pid;
                }

                // Wait for all children in this batch
                $statuses = [];
                foreach ($batchPids as $i => $pid) {
                    $statuses[$i] = $this->waitForChild($pid);
                }

                // Collect results for this batch
                foreach ($batchPids as $i => $pid) {
                    $results[$i] = $this->collectResult($i, $tempFiles[$i], $statuses[$i]);
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

    /**
     * Write the result envelope from the forked child. A missing or partial
     * write is detected by the parent when it fails to unserialize a complete
     * envelope from the file, so there is nothing useful to do on failure here.
     *
     * @codeCoverageIgnore Runs in the forked child only
     *
     * @param array{ok: bool, result?: mixed, error?: ParallelTaskError} $envelope
     */
    private static function writeChildResult(string $tempFile, array $envelope): void
    {
        @file_put_contents($tempFile, serialize($envelope));
    }

    private function collectResult(int $taskIndex, string $tempFile, int $status): mixed
    {
        $content = is_file($tempFile) ? file_get_contents($tempFile) : false;
        $envelope = ($content === false || $content === '') ? false : @unserialize($content);

        if (!is_array($envelope) || !array_key_exists('ok', $envelope)) {
            throw new Exception("Parallel task {$taskIndex} did not return a result ({$this->describeChildStatus($status)})");
        }

        if ($envelope['ok'] !== true) {
            $error = $envelope['error'] ?? null;
            $detail = $error instanceof ParallelTaskError ? $error->getSummary() : 'unknown error';

            throw new Exception("Parallel task {$taskIndex} failed: {$detail}");
        }

        return $envelope['result'] ?? null;
    }

    private function describeChildStatus(int $status): string
    {
        if (pcntl_wifsignaled($status)) {
            return 'child killed by signal ' . pcntl_wtermsig($status);
        }
        if (pcntl_wifexited($status)) {
            return 'child exited with code ' . pcntl_wexitstatus($status); // @codeCoverageIgnore
        }

        return 'child status unknown'; // @codeCoverageIgnore
    }

    private function waitForChild(int $pid): int
    {
        $startTime = time();

        while (true) {
            $status = 0;
            $result = pcntl_waitpid($pid, $status, WNOHANG);

            if ($result === $pid) {
                return is_int($status) ? $status : 0;
            }

            if ($result === -1) {
                return 0; // @codeCoverageIgnore
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
