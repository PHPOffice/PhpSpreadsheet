<?php

namespace PhpOffice\PhpSpreadsheet\Parallel\Backend;

use __PHP_Incomplete_Class;
use Closure;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\Parallel\CpuDetector;
use Throwable;

class PcntlBackend implements BackendInterface
{
    /** Seconds a child may run before being terminated; 0 means no time limit. */
    private const DEFAULT_TIMEOUT = 0;

    /** Seconds to wait after SIGTERM before escalating to SIGKILL. */
    private const SIGTERM_GRACE_SECONDS = 2;

    private int $timeout;

    public function __construct(int $timeout = self::DEFAULT_TIMEOUT)
    {
        $this->timeout = $timeout;
    }

    public function execute(array $tasks, Closure $worker, int $maxWorkers): array
    {
        if ($maxWorkers < 1) {
            throw new Exception('maxWorkers must be at least 1');
        }

        if (!self::isAvailable()) {
            throw new Exception('Forking is not available (requires the CLI SAPI, the pcntl extension, and the fidry/cpu-core-counter package)'); // @codeCoverageIgnore
        }

        $taskCount = count($tasks);
        $results = array_fill(0, $taskCount, null);
        $tempFiles = [];
        $pids = [];
        $reaped = [];

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
                        try {
                            $result = $worker($tasks[$i]);
                            self::writeChildResult($tempFile, ['ok' => true, 'result' => $result]);
                        } catch (Throwable $e) {
                            self::writeChildResult($tempFile, ['ok' => false, 'error' => ParallelTaskError::fromThrowable($e)]);
                        }
                        self::exitChild();
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
                    $reaped[$pid] = true;
                }

                // Collect results for this batch
                foreach ($batchPids as $i => $pid) {
                    $results[$i] = $this->collectResult($i, $tempFiles[$i], $statuses[$i]);
                }
            }
        } finally {
            // Children never reach this block — exitChild() terminates them —
            // so only the parent reaps and cleans up here. Children still
            // running (e.g. siblings of a timed-out task) are killed so they
            // are neither orphaned nor left as zombies.
            foreach ($pids as $pid) {
                if (isset($reaped[$pid])) {
                    continue;
                }
                if (function_exists('posix_kill')) {
                    posix_kill($pid, 9); // SIGKILL
                    pcntl_waitpid($pid, $status);
                } else {
                    pcntl_waitpid($pid, $status, WNOHANG); // @codeCoverageIgnore
                }
            }

            // Clean up temp files
            foreach ($tempFiles as $file) {
                if (is_file($file)) {
                    @unlink($file);
                }
            }
        }

        return array_values($results);
    }

    /**
     * Terminate the forked child immediately, without running destructors,
     * shutdown functions, or flushing output buffers inherited from the parent
     * process — the child's shutdown sequence must not tear down shared
     * resources such as database connections, or emit the parent's buffered
     * output a second time.
     *
     * @codeCoverageIgnore Runs in the forked child only
     */
    private static function exitChild(): never
    {
        if (function_exists('posix_kill') && function_exists('posix_getpid')) {
            posix_kill(posix_getpid(), 9); // SIGKILL — bypasses PHP shutdown
        }

        exit(0);
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
        $envelope = false;
        if ($content !== false && $content !== '') {
            try {
                // The temp file sits in the shared system temp directory, so
                // it must not be an object-injection vector: only the one
                // class the envelope legitimately carries may be instantiated
                $envelope = @unserialize($content, ['allowed_classes' => [ParallelTaskError::class]]);
            } catch (Throwable) {
                $envelope = false;
            }
        }

        if (!is_array($envelope) || !array_key_exists('ok', $envelope)) {
            throw new Exception("Parallel task {$taskIndex} did not return a result ({$this->describeChildStatus($status)})");
        }

        if ($envelope['ok'] !== true) {
            $error = $envelope['error'] ?? null;
            $detail = $error instanceof ParallelTaskError ? $error->getSummary() : 'unknown error';

            throw new Exception("Parallel task {$taskIndex} failed: {$detail}");
        }

        $result = $envelope['result'] ?? null;
        if ($result instanceof __PHP_Incomplete_Class) {
            throw new Exception("Parallel task {$taskIndex} returned an object; task results must serialize to scalars or arrays");
        }

        return $result;
    }

    private function describeChildStatus(int $status): string
    {
        if (pcntl_wifsignaled($status)) {
            return 'child killed by signal ' . pcntl_wtermsig($status);
        }
        // @codeCoverageIgnoreStart
        if (pcntl_wifexited($status)) {
            return 'child exited with code ' . pcntl_wexitstatus($status);
        }

        return 'child status unknown';
        // @codeCoverageIgnoreEnd
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

            if ($this->timeout > 0 && (time() - $startTime) >= $this->timeout) {
                $this->terminateChild($pid);

                throw new Exception("Parallel task timed out after {$this->timeout} seconds");
            }

            usleep(10000); // 10ms poll interval
        }
    }

    /**
     * Terminate a child with SIGTERM, escalating to SIGKILL if it has not
     * exited within the grace period, then reap it so it cannot linger as
     * an orphan or zombie.
     */
    private function terminateChild(int $pid): void
    {
        if (function_exists('posix_kill')) {
            posix_kill($pid, 15); // SIGTERM
            $deadline = microtime(true) + self::SIGTERM_GRACE_SECONDS;
            while (microtime(true) < $deadline) {
                if (pcntl_waitpid($pid, $status, WNOHANG) === $pid) {
                    return;
                }
                usleep(10000);
            }
            posix_kill($pid, 9); // SIGKILL
            pcntl_waitpid($pid, $status);

            return;
        }

        pcntl_waitpid($pid, $status, WNOHANG); // @codeCoverageIgnore
    }

    public static function isAvailable(): bool
    {
        // Forking is only safe from the CLI: under FPM or an Apache handler a
        // forked child would share the SAPI's sockets and process-pool state
        return PHP_SAPI === 'cli'
            && function_exists('pcntl_fork')
            && function_exists('pcntl_waitpid')
            && PHP_OS_FAMILY !== 'Windows'
            && CpuDetector::isAvailable();
    }
}
