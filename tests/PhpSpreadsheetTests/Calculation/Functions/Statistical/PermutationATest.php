<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical\Permutations;
use PHPUnit\Framework\TestCase;

class PermutationATest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerPERMUT
     *
     * @param mixed $expectedResult
     */
    public function testPERMUT($expectedResult, ...$args): void
    {
        $result = Permutations::PERMUTATIONA(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerPERMUT(): array
    {
        return require 'tests/data/Calculation/Statistical/PERMUTATIONA.php';
    }
}
