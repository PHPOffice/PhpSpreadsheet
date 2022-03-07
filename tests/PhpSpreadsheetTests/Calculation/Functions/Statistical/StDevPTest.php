<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical;
use PHPUnit\Framework\TestCase;

class StDevPTest extends TestCase
{
    protected function tearDown(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerSTDEVP
     *
     * @param mixed $expectedResult
     * @param mixed $values
     */
    public function testSTDEVP($expectedResult, $values): void
    {
        $result = Statistical::STDEVP($values);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerSTDEVP(): array
    {
        return require 'tests/data/Calculation/Statistical/STDEVP.php';
    }

    /**
     * @dataProvider providerOdsSTDEVP
     *
     * @param mixed $expectedResult
     * @param mixed $values
     */
    public function testOdsSTDEVP($expectedResult, $values): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_OPENOFFICE);

        $result = Statistical::STDEVP($values);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerOdsSTDEVP(): array
    {
        return require 'tests/data/Calculation/Statistical/STDEVP_ODS.php';
    }
}
