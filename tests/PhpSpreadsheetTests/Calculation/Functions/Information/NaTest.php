<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Information;

use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;
use PHPUnit\Framework\TestCase;

class NaTest extends TestCase
{
    public function testNA(): void
    {
        $result = ExcelError::NA();
        self::assertEquals('#N/A', $result);
    }
}
