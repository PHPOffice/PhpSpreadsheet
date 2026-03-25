<?php

namespace PhpOffice\PhpSpreadsheet\Parallel\Backend;

/**
 * Serializable error container passed from child to parent via IPC.
 */
class ParallelTaskError
{
    private string $message;

    private int $code;

    public function __construct(string $message, int $code = 0)
    {
        $this->message = $message;
        $this->code = $code;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getCode(): int
    {
        return $this->code;
    }
}
