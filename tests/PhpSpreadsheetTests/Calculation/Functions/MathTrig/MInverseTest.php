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
    public function testMINVERSE($expectedResult, ...$args)
    {
        $result = MathTrig::MINVERSE(...$args);
        $this->assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public function providerMINVERSE()
    {
        return require 'tests/data/Calculation/MathTrig/MINVERSE.php';
    }
}
