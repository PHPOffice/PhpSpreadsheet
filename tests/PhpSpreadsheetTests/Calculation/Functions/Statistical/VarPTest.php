<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical;
use PHPUnit\Framework\TestCase;

class VarPTest extends TestCase
{
    protected function tearDown(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerVARP
     *
     * @param mixed $expectedResult
     * @param mixed $values
     */
    public function testVARP($expectedResult, $values): void
    {
        $result = Statistical::VARP($values);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerVARP(): array
    {
        return require 'tests/data/Calculation/Statistical/VARP.php';
    }

    /**
     * @dataProvider providerOdsVARP
     *
     * @param mixed $expectedResult
     * @param mixed $values
     */
    public function testOdsVARP($expectedResult, $values): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_OPENOFFICE);

        $result = Statistical::VARP($values);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerOdsVARP(): array
    {
        return require 'tests/data/Calculation/Statistical/VARP_ODS.php';
    }
}
