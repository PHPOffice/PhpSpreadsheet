<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

use PhpOffice\PhpSpreadsheet\Calculation\Financial;
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
        $result = Financial::COUPDAYSNC(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public function providerCOUPDAYSNC(): array
    {
        return require 'tests/data/Calculation/Financial/COUPDAYSNC.php';
    }
}
