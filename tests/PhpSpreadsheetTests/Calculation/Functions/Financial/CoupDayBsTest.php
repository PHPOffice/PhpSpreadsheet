<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

use PhpOffice\PhpSpreadsheet\Calculation\Financial;
use PHPUnit\Framework\TestCase;

class CoupDayBsTest extends TestCase
{
    /**
     * @dataProvider providerCOUPDAYBS
     *
     * @param mixed $expectedResult
     */
    public function testCOUPDAYBS($expectedResult, ...$args): void
    {
        $result = Financial\Coupons::COUPDAYBS(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public function providerCOUPDAYBS(): array
    {
        return require 'tests/data/Calculation/Financial/COUPDAYBS.php';
    }
}
