<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical;
use PHPUnit\Framework\TestCase;

class StDevTest extends TestCase
{
    protected function tearDown(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerSTDEV
     *
     * @param mixed $expectedResult
     * @param mixed $values
     */
    public function testSTDEV($expectedResult, $values): void
    {
        $result = Statistical::STDEV($values);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerSTDEV(): array
    {
        return require 'tests/data/Calculation/Statistical/STDEV.php';
    }

    /**
     * @dataProvider providerOdsSTDEV
     *
     * @param mixed $expectedResult
     * @param mixed $values
     */
    public function testOdsSTDEV($expectedResult, $values): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_OPENOFFICE);

        $result = Statistical::STDEV($values);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerOdsSTDEV(): array
    {
        return require 'tests/data/Calculation/Statistical/STDEV_ODS.php';
    }
}
