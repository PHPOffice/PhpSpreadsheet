<?php

namespace PhpOffice\PhpSpreadsheet\Reader\Xlsx;

class SharedFormula
{
    private string $master;

    private string $formula;

    public function __construct(string $master, string $formula)
    {
        $this->master = $master;
        $this->formula = $formula;
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
