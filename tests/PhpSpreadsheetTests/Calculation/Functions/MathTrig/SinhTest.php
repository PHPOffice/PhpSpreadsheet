<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

class SinhTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerCosh
     *
     * @param mixed $expectedResult
     */
    public function testSinh($expectedResult, string $formula): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        $sheet->setCellValue('A2', 2);
        $sheet->getCell('A1')->setValue("=SINH($formula)");
        $result = $sheet->getCell('A1')->getCalculatedValue();
        self::assertEqualsWithDelta($expectedResult, $result, 1E-6);
    }

    public static function providerCosh(): array
    {
        return require 'tests/data/Calculation/MathTrig/SINH.php';
    }

    /**
     * @dataProvider providerSinhArray
     */
    public function testSinhArray(array $expectedResult, string $array): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=SINH({$array})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public static function providerSinhArray(): array
    {
        return [
            'row vector' => [[[1.17520119364380, 0.52109530549375, -1.17520119364380]], '{1, 0.5, -1}'],
            'column vector' => [[[1.17520119364380], [0.52109530549375], [-1.17520119364380]], '{1; 0.5; -1}'],
            'matrix' => [[[1.17520119364380, 0.52109530549375], [0.0, -1.17520119364380]], '{1, 0.5; 0, -1}'],
        ];
    }
}
