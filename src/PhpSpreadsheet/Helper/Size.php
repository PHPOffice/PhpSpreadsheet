<?php

namespace PhpOffice\PhpSpreadsheet\Helper;

use Stringable;

class Size implements Stringable
{
    const REGEXP_SIZE_VALIDATION = '/^(?P<size>\d*\.?\d+)(?P<unit>pt|px|em)?$/i';

    protected bool $valid;

    protected string $size = '';

    protected string $unit = '';

    public function __construct(string $size)
    {
        $this->valid = (bool) preg_match(self::REGEXP_SIZE_VALIDATION, $size, $matches);
        if ($this->valid) {
            $this->size = $matches['size'];
            $this->unit = $matches['unit'] ?? 'pt';
        }
    }

    public function valid(): bool
    {
        return $this->valid;
    }

    public function size(): string
    {
        return $this->size;
    }

    public function unit(): string
    {
        return $this->unit;
    }

    public function __toString(): string
    {
        return $this->size . $this->unit;
    }
}
