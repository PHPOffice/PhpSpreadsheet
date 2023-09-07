<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\MathTrig;

class MInverseTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerMINVERSE
     */
    public function testMINVERSE(mixed $expectedResult, array $args): void
    {
        $result = MathTrig\MatrixFunctions::inverse($args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public static function providerMINVERSE(): array
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
