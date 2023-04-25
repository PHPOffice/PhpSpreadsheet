<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\TextData;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

class LenTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerLEN
     *
     * @param mixed $expectedResult
     * @param mixed $str
     */
    public function testLEN($expectedResult, $str = 'omitted'): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        if ($str === 'omitted') {
            $sheet->getCell('B1')->setValue('=LEN()');
        } else {
            $this->setCell('A1', $str);
            $sheet->getCell('B1')->setValue('=LEN(A1)');
        }
        $result = $sheet->getCell('B1')->getCalculatedValue();
        self::assertEquals($expectedResult, $result);
    }

    public static function providerLEN(): array
    {
        return require 'tests/data/Calculation/TextData/LEN.php';
    }

    /**
     * @dataProvider providerLenArray
     */
    public function testLenArray(array $expectedResult, string $array): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=LEN({$array})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public static function providerLenArray(): array
    {
        return [
            'row vector' => [[[3, 11, 14]], '{"PHP", "Hello World", "PhpSpreadsheet"}'],
            'column vector' => [[[3], [11], [14]], '{"PHP"; "Hello World"; "PhpSpreadsheet"}'],
            'matrix' => [[[3, 9], [11, 14]], '{"PHP", "ElePHPant"; "Hello World", "PhpSpreadsheet"}'],
        ];
    }
}
