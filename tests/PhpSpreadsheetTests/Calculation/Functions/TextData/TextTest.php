<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\TextData;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

class TextTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerTEXT
     */
    public function testTEXT(mixed $expectedResult, mixed $value = 'omitted', mixed $format = 'omitted'): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        if ($value === 'omitted') {
            $sheet->getCell('B1')->setValue('=TEXT()');
        } elseif ($format === 'omitted') {
            $this->setCell('A1', $value);
            $sheet->getCell('B1')->setValue('=TEXT(A1)');
        } else {
            $this->setCell('A1', $value);
            $this->setCell('A2', $format);
            $sheet->getCell('B1')->setValue('=TEXT(A1, A2)');
        }
        $result = $sheet->getCell('B1')->getCalculatedValue();
        self::assertEquals($expectedResult, $result);
    }

    public static function providerTEXT(): array
    {
        return require 'tests/data/Calculation/TextData/TEXT.php';
    }

    /**
     * @dataProvider providerTextArray
     */
    public function testTextArray(array $expectedResult, string $argument1, string $argument2): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=TEXT({$argument1}, {$argument2})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public static function providerTextArray(): array
    {
        return [
            'row vector' => [[['123.75%', '1 19/80']], '1.2375', '{"0.00%", "0 ??/???"}'],
            'matrix vector' => [
                [
                    ['$ -1,234.57', '(1,234.57)'],
                    ['$ 9,876.54', '9,876.54'],
                ],
                '{-1234.5678; 9876.5432}',
                '{"$ #,##0.00", "#,##0.00;(#,##0.00)"}',
            ],
        ];
    }
}
