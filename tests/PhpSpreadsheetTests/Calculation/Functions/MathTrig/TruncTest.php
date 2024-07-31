<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

class TruncTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerTRUNC
     */
    public function testTRUNC(mixed $expectedResult, string $formula): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        $sheet->setCellValue('A2', 1.3);
        $sheet->setCellValue('A3', 2.7);
        $sheet->setCellValue('A4', -3.8);
        $sheet->setCellValue('A5', -5.2);
        $sheet->getCell('A1')->setValue("=TRUNC($formula)");
        $result = $sheet->getCell('A1')->getCalculatedValue();
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public static function providerTRUNC(): array
    {
        return require 'tests/data/Calculation/MathTrig/TRUNC.php';
    }

    /**
     * @dataProvider providerTruncArray
     */
    public function testTruncArray(array $expectedResult, string $argument1, string $argument2): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=TRUNC({$argument1}, {$argument2})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public static function providerTruncArray(): array
    {
        return [
            'matrix' => [[[3.14, 3.141], [3.14159, 3.14159265]], '3.1415926536', '{2, 3; 5, 8}'],
        ];
    }

    public function testTooMuchPrecision(): void
    {
        // This test is pretty screwy. Possibly shouldn't even attempt it.
        // At any rate, these results seem to indicate that PHP
        // maximum precision is PHP_FLOAT_DIG - 1 digits, not PHP_FLOAT_DIG.
        // If that changes, at least one of these tests will have to change.
        $sheet = $this->getSheet();
        $sheet->getCell('E1')->setValue(10.0 ** (PHP_FLOAT_DIG - 3) + 1.2);
        $sheet->getCell('E2')->setValue('=TRUNC(E1,1)');
        $result = $sheet->getCell('E2')->getCalculatedValue();
        $expectedResult = '1' . str_repeat('0', PHP_FLOAT_DIG - 4) . '1.2';
        self::assertSame($expectedResult, (string) $result);

        $sheet->getCell('F1')->setValue(10.0 ** (PHP_FLOAT_DIG - 2) + 1.2);
        $sheet->getCell('F2')->setValue('=TRUNC(F1,1)');
        $result = $sheet->getCell('F2')->getCalculatedValue();
        $expectedResult = '1' . str_repeat('0', PHP_FLOAT_DIG - 3) . '1';
        self::assertSame($expectedResult, (string) $result);

        $sheet->getCell('G1')->setValue(10.0 ** (PHP_FLOAT_DIG - 1) + 1.2);
        $sheet->getCell('G2')->setValue('=TRUNC(G1,1)');
        $result = $sheet->getCell('G2')->getCalculatedValue();
        $expectedResult = '1.0E+' . (PHP_FLOAT_DIG - 1);
        self::assertSame($expectedResult, (string) $result);

        $sheet->getCell('H1')->setValue(10.0 ** PHP_FLOAT_DIG + 1.2);
        $sheet->getCell('H2')->setValue('=TRUNC(H1,1)');
        $result = $sheet->getCell('H2')->getCalculatedValue();
        $expectedResult = '1.0E+' . PHP_FLOAT_DIG;
        self::assertSame($expectedResult, (string) $result);
    }
}
