<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Engineering;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PHPUnit\Framework\TestCase;

class Oct2DecTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerOCT2DEC
     *
     * @param mixed $expectedResult
     */
    public function testOCT2DEC($expectedResult, ...$args): void
    {
        $result = Engineering::OCTTODEC(...$args);
        self::assertEquals($expectedResult, $result);
    }

    public function providerOCT2DEC()
    {
        return require 'tests/data/Calculation/Engineering/OCT2DEC.php';
    }
}
