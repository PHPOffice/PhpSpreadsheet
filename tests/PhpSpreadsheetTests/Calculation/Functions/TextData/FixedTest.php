<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\TextData;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

class FixedTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerFIXED
     */
    public function testFIXED(mixed $expectedResult, mixed $number = 'omitted', mixed $decimals = 'omitted', mixed $noCommas = 'omitted'): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        if ($number === 'omitted') {
            $sheet->getCell('B1')->setValue('=FIXED()');
        } elseif ($decimals === 'omitted') {
            $this->setCell('A1', $number);
            $sheet->getCell('B1')->setValue('=FIXED(A1)');
        } elseif ($noCommas === 'omitted') {
            $this->setCell('A1', $number);
            $this->setCell('A2', $decimals);
            $sheet->getCell('B1')->setValue('=FIXED(A1, A2)');
        } else {
            $this->setCell('A1', $number);
            $this->setCell('A2', $decimals);
            $this->setCell('A3', $noCommas);
            $sheet->getCell('B1')->setValue('=FIXED(A1, A2, A3)');
        }
        $result = $sheet->getCell('B1')->getCalculatedValue();
        self::assertEquals($expectedResult, $result);
    }

    public static function providerFIXED(): array
    {
        return require 'tests/data/Calculation/TextData/FIXED.php';
    }

    /**
     * @dataProvider providerFixedArray
     */
    public function testFixedArray(array $expectedResult, string $argument1, string $argument2): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=FIXED({$argument1}, {$argument2})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public static function providerFixedArray(): array
    {
        return [
            'row vector #1' => [[['-123.32', '123.46', '12,345.68']], '{-123.321, 123.456, 12345.6789}', '2'],
            'column vector #1' => [[['-123.32'], ['123.46'], ['12,345.68']], '{-123.321; 123.456; 12345.6789}', '2'],
            'matrix #1' => [[['-123.46', '12,345.68'], ['-123.456', '12,345.679']], '{-123.456, 12345.6789}', '{2; 3}'],
        ];
    }
}
