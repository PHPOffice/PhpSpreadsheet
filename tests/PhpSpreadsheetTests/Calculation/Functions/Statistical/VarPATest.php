<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical;
use PHPUnit\Framework\TestCase;

class VarPATest extends TestCase
{
    protected function tearDown(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerVARPA
     *
     * @param mixed $expectedResult
     * @param mixed $values
     */
    public function testVARPA($expectedResult, $values): void
    {
        $result = Statistical::VARPA($values);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerVARPA(): array
    {
        return require 'tests/data/Calculation/Statistical/VARPA.php';
    }

    /**
     * @dataProvider providerOdsVARPA
     *
     * @param mixed $expectedResult
     * @param mixed $values
     */
    public function testOdsVARPA($expectedResult, $values): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_OPENOFFICE);

        $result = Statistical::VARPA($values);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerOdsVARPA(): array
    {
        return require 'tests/data/Calculation/Statistical/VARPA_ODS.php';
    }
}
