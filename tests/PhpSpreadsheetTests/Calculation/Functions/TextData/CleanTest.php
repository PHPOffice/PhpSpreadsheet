<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\TextData;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

class CleanTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerCLEAN
     *
     * @param mixed $expectedResult
     * @param mixed $value
     */
    public function testCLEAN($expectedResult, $value = 'omitted'): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        if ($value === 'omitted') {
            $sheet->getCell('B1')->setValue('=CLEAN()');
        } else {
            $this->setCell('A1', $value);
            $sheet->getCell('B1')->setValue('=CLEAN(A1)');
        }
        $result = $sheet->getCell('B1')->getCalculatedValue();
        self::assertEquals($expectedResult, $result);
    }

    public static function providerCLEAN(): array
    {
        return require 'tests/data/Calculation/TextData/CLEAN.php';
    }

    /**
     * @dataProvider providerCleanArray
     */
    public function testCleanArray(array $expectedResult, string $array): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=CLEAN({$array})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public static function providerCleanArray(): array
    {
        return [
            'row vector' => [[['PHP', 'MS Excel', 'Open/Libre Office']], '{"PHP", "MS Excel", "Open/Libre Office"}'],
            'column vector' => [[['PHP'], ['MS Excel'], ['Open/Libre Office']], '{"PHP"; "MS Excel"; "Open/Libre Office"}'],
            'matrix' => [[['PHP', 'MS Excel'], ['PhpSpreadsheet', 'Open/Libre Office']], '{"PHP", "MS Excel"; "PhpSpreadsheet", "Open/Libre Office"}'],
        ];
    }
}
