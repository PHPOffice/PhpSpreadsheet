<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig;
use PHPUnit\Framework\TestCase;

class BaseTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerBASE
     *
     * @param mixed $expectedResult
     */
    public function testBASE($expectedResult, ...$args): void
    {
        $result = MathTrig::BASE(...$args);
        self::assertEquals($expectedResult, $result);
    }

    public function providerBASE()
    {
        return require 'tests/data/Calculation/MathTrig/BASE.php';
    }
}
