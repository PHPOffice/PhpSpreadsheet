<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Information;

use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;
use PHPUnit\Framework\TestCase;

class NullTest extends TestCase
{
    public function testNULL(): void
    {
        $result = ExcelError::null();
        self::assertEquals('#NULL!', $result);
    }
}
