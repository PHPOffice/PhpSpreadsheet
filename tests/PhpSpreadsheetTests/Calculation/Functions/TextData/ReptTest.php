<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\TextData;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

class ReptTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerREPT
     */
    public function testReptThroughEngine(mixed $expectedResult, mixed $val = 'omitted', mixed $rpt = 'omitted'): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        if ($val === 'omitted') {
            $sheet->getCell('B1')->setValue('=REPT()');
        } elseif ($rpt === 'omitted') {
            $this->setCell('A1', $val);
            $sheet->getCell('B1')->setValue('=REPT(A1)');
        } else {
            $this->setCell('A1', $val);
            $this->setCell('A2', $rpt);
            $sheet->getCell('B1')->setValue('=REPT(A1, A2)');
        }
        $result = $sheet->getCell('B1')->getCalculatedValue();
        self::assertEquals($expectedResult, $result);
    }

    public static function providerREPT(): array
    {
        return require 'tests/data/Calculation/TextData/REPT.php';
    }

    /**
     * @dataProvider providerReptArray
     */
    public function testReptArray(array $expectedResult, string $argument1, string $argument2): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=REPT({$argument1}, {$argument2})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public static function providerReptArray(): array
    {
        return [
            'row vector #1' => [[['PHPPHPPHP', 'HAHAHA', 'HOHOHO']], '{"PHP", "HA", "HO"}', '3'],
            'column vector #1' => [[['PHPPHPPHP'], ['HAHAHA'], ['HOHOHO']], '{"PHP"; "HA"; "HO"}', '3'],
            'matrix #1' => [[['PHPPHP', '❤️🐘💚❤️🐘💚'], ['HAHA', 'HOHO']], '{"PHP", "❤️🐘💚"; "HA", "HO"}', '2'],
            'row vector #2' => [[[' PHP  PHP  PHP ', ' PHP  PHP ']], '" PHP "', '{3, 2}'],
        ];
    }
}
