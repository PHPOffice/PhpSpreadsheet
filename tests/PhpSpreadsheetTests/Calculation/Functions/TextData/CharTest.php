<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\TextData;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

class CharTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerCHAR
     *
     * @param mixed $expectedResult
     * @param mixed $character
     */
    public function testCHAR($expectedResult, $character = 'omitted'): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        if ($character === 'omitted') {
            $sheet->getCell('B1')->setValue('=CHAR()');
        } else {
            $this->setCell('A1', $character);
            $sheet->getCell('B1')->setValue('=CHAR(A1)');
        }
        $result = $sheet->getCell('B1')->getCalculatedValue();
        self::assertEquals($expectedResult, $result);
    }

    public static function providerCHAR(): array
    {
        return require 'tests/data/Calculation/TextData/CHAR.php';
    }

    /**
     * @dataProvider providerCharArray
     */
    public function testCharArray(array $expectedResult, string $array): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=CHAR({$array})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public static function providerCharArray(): array
    {
        return [
            'row vector' => [[['P', 'H', 'P']], '{80, 72, 80}'],
            'column vector' => [[['P'], ['H'], ['P']], '{80; 72; 80}'],
            'matrix' => [[['Y', 'o'], ['l', 'o']], '{89, 111; 108, 111}'],
        ];
    }
}
