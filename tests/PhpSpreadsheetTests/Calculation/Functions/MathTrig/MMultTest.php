<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\MathTrig;

class MMultTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerMMULT
     *
     * @param mixed $expectedResult
     */
    public function testMMULT($expectedResult, ...$args): void
    {
        $result = MathTrig::MMULT(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public function providerMMULT()
    {
        return require 'tests/data/Calculation/MathTrig/MMULT.php';
    }

    public function testOnSpreadsheet(): void
    {
        // very limited ability to test this in the absence of dynamic arrays
        $sheet = $this->sheet;
        $sheet->getCell('A1')->setValue('=MMULT({1,2,3}, {1,2,3})'); // incompatible dimensions
        self::assertSame('#VALUE!', $sheet->getCell('A1')->getCalculatedValue());
    }
}
