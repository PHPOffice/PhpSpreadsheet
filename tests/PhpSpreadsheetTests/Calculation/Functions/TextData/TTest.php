<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\TextData;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

class TTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerT
     *
     * @param mixed $expectedResult
     * @param mixed $value
     */
    public function testT($expectedResult, $value): void
    {
        $worksheet = $this->getSheet();
        $this->setCell('A1', $value);
        $worksheet->getCell('H1')->setValue('=T(A1)');
        $result = $worksheet->getCell('H1')->getCalculatedValue();
        self::assertEquals($expectedResult, $result);
    }

    public function providerT(): array
    {
        return require 'tests/data/Calculation/TextData/T.php';
    }

    /**
     * @dataProvider providerTArray
     */
    public function testTArray(array $expectedResult, string $argument): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=T({$argument})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public function providerTArray(): array
    {
        return [
            'row vector #1' => [[['PHP', null, 'PHP8']], '{"PHP", 99, "PHP8"}'],
            'column vector #1' => [[[null], ['PHP'], [null]], '{12; "PHP"; 1.2}'],
            'matrix #1' => [[['TRUE', 'FALSE'], [null, null]], '{"TRUE", "FALSE"; TRUE, FALSE}'],
        ];
    }
}
