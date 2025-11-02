<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\DateTime;

use PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\Helpers;
use PhpOffice\PhpSpreadsheet\Calculation\Exception as CalcExp;
use PHPUnit\Framework\TestCase;

class HelpersTest extends TestCase
{
    public function testGetDateValueBadObject(): void
    {
        $this->expectException(CalcExp::class);
        $this->expectExceptionMessage('#VALUE!');
        Helpers::getDateValue($this);
    }
}
