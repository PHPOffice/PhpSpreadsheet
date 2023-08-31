<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

class SteyxTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerSTEYX
     *
     * @param mixed $expectedResult
     */
    public function testSTEYX($expectedResult, array $xargs, array $yargs): void
    {
        //$result = Statistical\Trends::STEYX($xargs, $yargs);
        //self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
        $this->runTestCaseReference('STEYX', $expectedResult, $xargs, $yargs);
    }

    public static function providerSTEYX(): array
    {
        return require 'tests/data/Calculation/Statistical/STEYX.php';
    }
}
