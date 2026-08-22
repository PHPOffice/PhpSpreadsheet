<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Statistical;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

// TODO Convert to Spreadsheet context.
class ChiTestTest extends TestCase
{
    /**
     * @param float[] $actual
     * @param float[] $expected
     */
    #[DataProvider('providerCHITEST')]
    public function testCHITEST(mixed $expectedResult, array $actual, array $expected): void
    {
        $result = Statistical\Distributions\ChiSquared::test($actual, $expected);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public static function providerCHITEST(): array
    {
        return require 'tests/data/Calculation/Statistical/CHITEST.php';
    }
}
