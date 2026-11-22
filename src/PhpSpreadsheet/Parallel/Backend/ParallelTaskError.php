<?php

namespace PhpOffice\PhpSpreadsheet\Parallel\Backend;

use Throwable;

/**
 * Serializable error container passed from child to parent via IPC.
 */
class ParallelTaskError
{
    private string $message;

    private int $code;

    private string $exceptionClass;

    private string $traceAsString;

    public function __construct(string $message, int $code = 0, string $exceptionClass = '', string $traceAsString = '')
    {
        $this->message = $message;
        $this->code = $code;
        $this->exceptionClass = $exceptionClass;
        $this->traceAsString = $traceAsString;
    }

    public static function fromThrowable(Throwable $e): self
    {
        return new self($e->getMessage(), (int) $e->getCode(), get_class($e), $e->getTraceAsString());
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getCode(): int
    {
        return $this->code;
    }

    public function getExceptionClass(): string
    {
        return $this->exceptionClass;
    }

    public function getTraceAsString(): string
    {
        return $this->traceAsString;
    }

    public function getSummary(): string
    {
        $summary = $this->exceptionClass !== '' ? "[{$this->exceptionClass}] {$this->message}" : $this->message;
        if ($this->traceAsString !== '') {
            $summary .= "\n" . $this->traceAsString;
        }

        return $summary;
    }
}
