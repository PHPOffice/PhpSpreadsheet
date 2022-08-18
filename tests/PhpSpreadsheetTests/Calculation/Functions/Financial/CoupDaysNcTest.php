<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

use PhpOffice\PhpSpreadsheet\Calculation\Financial\Coupons;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PHPUnit\Framework\TestCase;

class CoupDaysNcTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerCOUPDAYSNC
     *
     * @param mixed $expectedResult
     */
    public function testCOUPDAYSNC($expectedResult, ...$args): void
    {
        $result = Coupons::daysToNextCoupon(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public function providerCOUPDAYSNC(): array
    {
        return require 'tests/data/Calculation/Financial/COUPDAYSNC.php';
    }
}
