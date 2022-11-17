<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

use PhpOffice\PhpSpreadsheet\Calculation\Financial;
use PHPUnit\Framework\TestCase;

class AmorDegRcTest extends TestCase
{
    /**
     * @dataProvider providerAMORDEGRC
     *
     * @param mixed $expectedResult
     */
    public function testAMORDEGRC($expectedResult, ...$args): void
    {
        $result = Financial\Amortization::AMORDEGRC(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public function providerAMORDEGRC(): array
    {
        return require 'tests/data/Calculation/Financial/AMORDEGRC.php';
    }
}
