<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\MathTrig;

class MMultTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerMMULT
     */
    public function testMMULT(mixed $expectedResult, mixed ...$args): void
    {
        $result = MathTrig\MatrixFunctions::multiply(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public static function providerMMULT(): array
    {
        return require 'tests/data/Calculation/MathTrig/MMULT.php';
    }

    public function testOnSpreadsheet(): void
    {
        // very limited ability to test this in the absence of dynamic arrays
        $sheet = $this->getSheet();
        $sheet->getCell('A1')->setValue('=MMULT({1,2,3}, {1,2,3})'); // incompatible dimensions
        self::assertSame('#VALUE!', $sheet->getCell('A1')->getCalculatedValue());

        $sheet->getCell('A11')->setValue('=MMULT({1, 2, 3, 4}, {5; 6; 7; 8})');
        self::assertEquals(70, $sheet->getCell('A11')->getCalculatedValue());
        $sheet->getCell('A2')->setValue(1);
        $sheet->getCell('B2')->setValue(2);
        $sheet->getCell('C2')->setValue(3);
        $sheet->getCell('D2')->setValue(4);
        $sheet->getCell('D3')->setValue(5);
        $sheet->getCell('D4')->setValue(6);
        $sheet->getCell('A12')->setValue('=MMULT(A2:C2,D2:D4)');
        self::assertEquals(32, $sheet->getCell('A12')->getCalculatedValue());
    }
}
