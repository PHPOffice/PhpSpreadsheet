<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

use PhpOffice\PhpSpreadsheet\Calculation\Financial;
use PHPUnit\Framework\TestCase;

class CoupDaysTest extends TestCase
{
    /**
     * @dataProvider providerCOUPDAYS
     *
     * @param mixed $expectedResult
     */
    public function testCOUPDAYS($expectedResult, ...$args): void
    {
        $result = Financial\Coupons::COUPDAYS(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public function providerCOUPDAYS(): array
    {
        return require 'tests/data/Calculation/Financial/COUPDAYS.php';
    }
}
