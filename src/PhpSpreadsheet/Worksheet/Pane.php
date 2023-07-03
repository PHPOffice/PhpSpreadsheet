<?php

namespace PhpOffice\PhpSpreadsheet\Worksheet;

class Pane
{
    private string $sqref = '';

    private string $activeCell = '';

    private string $position;

    public function __construct(string $position, string $sqref = '', string $activeCell = '')
    {
        $this->sqref = $sqref;
        $this->activeCell = $activeCell;
        $this->position = $position;
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
