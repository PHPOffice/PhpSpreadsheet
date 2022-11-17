<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

use PhpOffice\PhpSpreadsheet\Calculation\Financial;
use PHPUnit\Framework\TestCase;

class CoupDaysNcTest extends TestCase
{
    /**
     * @dataProvider providerCOUPDAYSNC
     *
     * @param mixed $expectedResult
     */
    public function testCOUPDAYSNC($expectedResult, ...$args): void
    {
        $result = Financial\Coupons::COUPDAYSNC(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public function providerCOUPDAYSNC(): array
    {
        return require 'tests/data/Calculation/Financial/COUPDAYSNC.php';
    }
}
