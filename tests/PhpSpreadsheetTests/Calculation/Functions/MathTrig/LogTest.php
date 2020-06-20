<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig;
use PHPUnit\Framework\TestCase;

class LogTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerLOG
     *
     * @param mixed $expectedResult
     */
    public function testLOG($expectedResult, ...$args): void
    {
        $result = MathTrig::logBase(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerLOG()
    {
        return require 'tests/data/Calculation/MathTrig/LOG.php';
    }
}
