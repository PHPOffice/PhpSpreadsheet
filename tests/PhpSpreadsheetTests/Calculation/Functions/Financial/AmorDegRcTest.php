<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

use PhpOffice\PhpSpreadsheet\Calculation\Financial\Amortization;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PHPUnit\Framework\TestCase;

class AmorDegRcTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerAMORDEGRC
     *
     * @param mixed $expectedResult
     */
    public function testAMORDEGRC($expectedResult, ...$args): void
    {
        $result = Amortization::withCoefficient(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public function providerAMORDEGRC(): array
    {
        return require 'tests/data/Calculation/Financial/AMORDEGRC.php';
    }
}
