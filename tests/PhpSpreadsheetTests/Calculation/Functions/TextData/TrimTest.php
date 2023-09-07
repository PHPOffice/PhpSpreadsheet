<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\TextData;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

class TrimTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerTRIM
     */
    public function testTRIM(mixed $expectedResult, mixed $character = 'omitted'): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        if ($character === 'omitted') {
            $sheet->getCell('B1')->setValue('=TRIM()');
        } else {
            $this->setCell('A1', $character);
            $sheet->getCell('B1')->setValue('=TRIM(A1)');
        }
        $result = $sheet->getCell('B1')->getCalculatedValue();
        self::assertEquals($expectedResult, $result);
    }

    public static function providerTRIM(): array
    {
        return require 'tests/data/Calculation/TextData/TRIM.php';
    }

    /**
     * @dataProvider providerTrimArray
     */
    public function testTrimArray(array $expectedResult, string $array): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=TRIM({$array})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public static function providerTrimArray(): array
    {
        return [
            'row vector' => [[['PHP', 'MS Excel', 'Open/Libre Office']], '{"  PHP ", " MS   Excel ", " Open/Libre   Office "}'],
            'column vector' => [[['PHP'], ['MS Excel'], ['Open/Libre Office']], '{"  PHP "; " MS   Excel "; " Open/Libre   Office "}'],
            'matrix' => [[['PHP', 'MS Excel'], ['PhpSpreadsheet', 'Open/Libre Office']], '{"  PHP ", " MS   Excel "; " PhpSpreadsheet  ", " Open/Libre   Office "}'],
        ];
    }
}
