<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\TextData;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

class ReplaceTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerREPLACE
     *
     * @param mixed $expectedResult
     * @param mixed $oldText
     * @param mixed $start
     * @param mixed $count
     * @param mixed $newText
     */
    public function testREPLACE($expectedResult, $oldText = 'omitted', $start = 'omitted', $count = 'omitted', $newText = 'omitted'): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        if ($oldText === 'omitted') {
            $sheet->getCell('B1')->setValue('=REPLACE()');
        } elseif ($start === 'omitted') {
            $this->setCell('A1', $oldText);
            $sheet->getCell('B1')->setValue('=REPLACE(A1)');
        } elseif ($count === 'omitted') {
            $this->setCell('A1', $oldText);
            $this->setCell('A2', $start);
            $sheet->getCell('B1')->setValue('=REPLACE(A1, A2)');
        } elseif ($newText === 'omitted') {
            $this->setCell('A1', $oldText);
            $this->setCell('A2', $start);
            $this->setCell('A3', $count);
            $sheet->getCell('B1')->setValue('=REPLACE(A1, A2, A3)');
        } else {
            $this->setCell('A1', $oldText);
            $this->setCell('A2', $start);
            $this->setCell('A3', $count);
            $this->setCell('A4', $newText);
            $sheet->getCell('B1')->setValue('=REPLACE(A1, A2, A3, A4)');
        }
        $result = $sheet->getCell('B1')->getCalculatedValue();
        self::assertEquals($expectedResult, $result);
    }

    public static function providerREPLACE(): array
    {
        return require 'tests/data/Calculation/TextData/REPLACE.php';
    }

    /**
     * @dataProvider providerReplaceArray
     */
    public function testReplaceArray(
        array $expectedResult,
        string $oldText,
        string $start,
        string $chars,
        string $newText
    ): void {
        $calculation = Calculation::getInstance();

        $formula = "=REPLACE({$oldText}, {$start}, {$chars}, {$newText})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public static function providerReplaceArray(): array
    {
        return [
            'row vector' => [[['Elephpant', 'ElePHPant']], '"Elephant"', '4', '2', '{"php", "PHP"}'],
        ];
    }
}
