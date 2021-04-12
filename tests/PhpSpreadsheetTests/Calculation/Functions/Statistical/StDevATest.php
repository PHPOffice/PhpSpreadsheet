<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical;
use PHPUnit\Framework\TestCase;

class StDevATest extends TestCase
{
    protected function tearDown(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerSTDEVA
     *
     * @param mixed $expectedResult
     * @param mixed $values
     */
    public function testSTDEVA($expectedResult, $values): void
    {
        $result = Statistical::STDEVA($values);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerSTDEVA(): array
    {
        return require 'tests/data/Calculation/Statistical/STDEVA.php';
    }

    /**
     * @dataProvider providerOdsSTDEVA
     *
     * @param mixed $expectedResult
     * @param mixed $values
     */
    public function testOdsSTDEVA($expectedResult, $values): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_OPENOFFICE);

        $result = Statistical::STDEVA($values);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerOdsSTDEVA(): array
    {
        return require 'tests/data/Calculation/Statistical/STDEVA_ODS.php';
    }
}
