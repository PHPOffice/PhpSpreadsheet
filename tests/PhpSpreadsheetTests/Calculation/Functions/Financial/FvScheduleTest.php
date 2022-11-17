<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

use PhpOffice\PhpSpreadsheet\Calculation\Financial;
use PHPUnit\Framework\TestCase;

class FvScheduleTest extends TestCase
{
    /**
     * @dataProvider providerFVSCHEDULE
     *
     * @param mixed $expectedResult
     */
    public function testFVSCHEDULE($expectedResult, ...$args): void
    {
        $result = Financial\CashFlow\Single::futureValue(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public function providerFVSCHEDULE(): array
    {
        return require 'tests/data/Calculation/Financial/FVSCHEDULE.php';
    }
}
