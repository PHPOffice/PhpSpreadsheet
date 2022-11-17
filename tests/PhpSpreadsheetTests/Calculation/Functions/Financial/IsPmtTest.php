<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

use PhpOffice\PhpSpreadsheet\Calculation\Financial;
use PHPUnit\Framework\TestCase;

class IsPmtTest extends TestCase
{
    /**
     * @dataProvider providerISPMT
     *
     * @param mixed $expectedResult
     */
    public function testISPMT($expectedResult, ...$args): void
    {
        $result = Financial\CashFlow\Constant\Periodic\Interest::schedulePayment(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public function providerISPMT(): array
    {
        return require 'tests/data/Calculation/Financial/ISPMT.php';
    }
}
