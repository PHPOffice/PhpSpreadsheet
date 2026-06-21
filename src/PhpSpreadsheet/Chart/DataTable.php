<?php

namespace PhpOffice\PhpSpreadsheet\Chart;

class DataTable
{
    private bool $showHorizontalBorder = true;

    private bool $showVerticalBorder = true;

    private bool $showOutline = true;

    private bool $showKeys = true;

    public function getShowHorizontalBorder(): bool
    {
        return $this->showHorizontalBorder;
    }

    public function getShowVerticalBorder(): bool
    {
        return $this->showVerticalBorder;
    }

    public function getShowOutline(): bool
    {
        return $this->showOutline;
    }

    public function getShowKeys(): bool
    {
        return $this->showKeys;
    }

    public function setShowHorizontalBorder(bool $showHorizontalBorder): self
    {
        $this->showHorizontalBorder = $showHorizontalBorder;

        return $this;
    }

    public function setShowVerticalBorder(bool $showVerticalBorder): self
    {
        $this->showVerticalBorder = $showVerticalBorder;

        return $this;
    }

    public function setShowOutline(bool $showOutline): self
    {
        $this->showOutline = $showOutline;

        return $this;
    }

    public function setShowKeys(bool $showKeys): self
    {
        $this->showKeys = $showKeys;

        return $this;
    }
}
