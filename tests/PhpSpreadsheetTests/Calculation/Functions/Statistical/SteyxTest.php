<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PHPUnit\Framework\Attributes\DataProvider;

class SteyxTest extends AllSetupTeardown
{
    /**
     * @param mixed[] $xargs
     * @param mixed[] $yargs
     */
    #[DataProvider('providerSTEYX')]
    public function testSTEYX(mixed $expectedResult, array $xargs, array $yargs): void
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
