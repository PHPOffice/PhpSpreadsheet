<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\MathTrig;

class MInverseTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerMINVERSE
     *
     * @param mixed $expectedResult
     */
    public function testMINVERSE($expectedResult, array $args): void
    {
        $result = MathTrig\MatrixFunctions::inverse($args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public function providerMINVERSE(): array
    {
        return require 'tests/data/Calculation/MathTrig/MINVERSE.php';
    }

    public function testOnSpreadsheet(): void
    {
        // very limited ability to test this in the absence of dynamic arrays
        $sheet = $this->getSheet();
        $sheet->getCell('A1')->setValue('=MINVERSE({1,2,3})'); // not square
        self::assertSame('#VALUE!', $sheet->getCell('A1')->getCalculatedValue());
    }
}
