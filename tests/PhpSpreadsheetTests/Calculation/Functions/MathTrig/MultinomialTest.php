<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig;
use PHPUnit\Framework\TestCase;

class MultinomialTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerMULTINOMIAL
     *
     * @param mixed $expectedResult
     */
    public function testMULTINOMIAL($expectedResult, ...$args): void
    {
        $result = MathTrig::MULTINOMIAL(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerMULTINOMIAL()
    {
        return require 'tests/data/Calculation/MathTrig/MULTINOMIAL.php';
    }
}
