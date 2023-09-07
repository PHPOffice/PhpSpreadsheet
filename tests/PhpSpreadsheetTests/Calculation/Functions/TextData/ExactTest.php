<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\TextData;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

class ExactTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerEXACT
     */
    public function testEXACT(mixed $expectedResult, mixed $string1 = 'omitted', mixed $string2 = 'omitted'): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        if ($string1 === 'omitted') {
            $sheet->getCell('B1')->setValue('=EXACT()');
        } elseif ($string2 === 'omitted') {
            $this->setCell('A1', $string1);
            $sheet->getCell('B1')->setValue('=EXACT(A1)');
        } else {
            $this->setCell('A1', $string1);
            $this->setCell('A2', $string2);
            $sheet->getCell('B1')->setValue('=EXACT(A1, A2)');
        }
        $result = $sheet->getCell('B1')->getCalculatedValue();
        self::assertEquals($expectedResult, $result);
    }

    public static function providerEXACT(): array
    {
        return require 'tests/data/Calculation/TextData/EXACT.php';
    }

    /**
     * @dataProvider providerExactArray
     */
    public function testExactArray(array $expectedResult, string $argument1, string $argument2): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=EXACT({$argument1}, {$argument2})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public static function providerExactArray(): array
    {
        return [
            'row vector #1' => [[[true, false, false]], '{"PHP", "php", "PHP8"}', '"PHP"'],
            'column vector #1' => [[[false], [true], [false]], '{"php"; "PHP"; "PHP8"}', '"PHP"'],
            'matrix #1' => [[[false, true], [false, true]], '{"TRUE", "FALSE"; TRUE, FALSE}', '"FALSE"'],
        ];
    }
}
