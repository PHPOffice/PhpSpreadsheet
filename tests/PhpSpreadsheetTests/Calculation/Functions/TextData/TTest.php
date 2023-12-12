<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\TextData;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

class TTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerT
     */
    public function testT(mixed $expectedResult, mixed $value = 'no arguments'): void
    {
        $this->mightHaveException($expectedResult);
        if ($value === 'no arguments') {
            $this->setCell('H1', '=T()');
        } else {
            $this->setCell('A1', $value);
            $this->setCell('H1', '=T(A1)');
        }
        $result = $this->getSheet()->getCell('H1')->getCalculatedValue();
        self::assertSame($expectedResult, $result);
    }

    public static function providerT(): array
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
        self::assertSame($expectedResult, $result);
    }

    public static function providerTArray(): array
    {
        return [
            'row vector #1' => [[['PHP', '', 'PHP8']], '{"PHP", 99, "PHP8"}'],
            'column vector #1' => [[[''], ['PHP'], ['']], '{12; "PHP"; 1.2}'],
            'matrix #1' => [[['TRUE', 'FALSE'], ['', '']], '{"TRUE", "FALSE"; TRUE, FALSE}'],
        ];
    }
}
