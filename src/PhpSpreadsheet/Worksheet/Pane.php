<?php

namespace PhpOffice\PhpSpreadsheet\Worksheet;

class Pane
{
    public function __construct(private string $position, private string $sqref = '', private string $activeCell = '')
    {
    }

    public function getPosition(): string
    {
        return $this->position;
    }

    public function getSqref(): string
    {
        return $this->sqref;
    }

    public function setSqref(string $sqref): self
    {
        $this->sqref = $sqref;

        return $this;
    }

    public function getActiveCell(): string
    {
        return $this->activeCell;
    }

    public function setActiveCell(string $activeCell): self
    {
        $this->activeCell = $activeCell;

        return $this;
    }
}
