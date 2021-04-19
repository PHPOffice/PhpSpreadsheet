<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

use PhpOffice\PhpSpreadsheet\Calculation\Financial;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PHPUnit\Framework\TestCase;

class AmorLincTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerAMORLINC
     *
     * @param mixed $expectedResult
     */
    public function testAMORLINC($expectedResult, ...$args): void
    {
        $result = Financial::AMORLINC(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public function providerAMORLINC(): array
    {
        return require 'tests/data/Calculation/Financial/AMORLINC.php';
    }
}
