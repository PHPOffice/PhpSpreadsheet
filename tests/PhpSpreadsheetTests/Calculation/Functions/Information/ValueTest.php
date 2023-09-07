<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Information;

use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;
use PHPUnit\Framework\TestCase;

class ValueTest extends TestCase
{
    public function testVALUE(): void
    {
        $result = ExcelError::VALUE();
        self::assertEquals('#VALUE!', $result);
    }
}
