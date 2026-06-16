<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Style\NumberFormat\Wizard;

use PhpOffice\PhpSpreadsheet\Style\NumberFormat\Wizard\NumberBase;

class NumberBase2 extends NumberBase
{
    protected function getLocaleFormat(): string
    {
        return 'none';
    }
}
