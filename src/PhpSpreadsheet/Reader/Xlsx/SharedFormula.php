<?php

namespace PhpOffice\PhpSpreadsheet\Reader\Xlsx;

class SharedFormula
{
    public function __construct(private string $master, private string $formula)
    {
    }

    public function master(): string
    {
        return $this->master;
    }

    public function formula(): string
    {
        return $this->formula;
    }
}
