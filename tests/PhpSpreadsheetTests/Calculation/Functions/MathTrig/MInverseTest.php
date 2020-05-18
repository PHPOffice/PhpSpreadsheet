<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig;
use PHPUnit\Framework\TestCase;

class MInverseTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerMINVERSE
     *
     * @param mixed $expectedResult
     */
    public function testMINVERSE($expectedResult, ...$args): void
    {
        $result = MathTrig::MINVERSE(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public function providerMINVERSE()
    {
        return require 'tests/data/Calculation/MathTrig/MINVERSE.php';
    }
}
