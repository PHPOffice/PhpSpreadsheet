<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

use PhpOffice\PhpSpreadsheet\Calculation\Financial;
use PHPUnit\Framework\TestCase;

class AmorLincTest extends TestCase
{
    /**
     * @dataProvider providerAMORLINC
     *
     * @param mixed $expectedResult
     */
    public function testAMORLINC($expectedResult, ...$args): void
    {
        $result = Financial\Amortization::AMORLINC(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public function providerAMORLINC(): array
    {
        return require 'tests/data/Calculation/Financial/AMORLINC.php';
    }
}
