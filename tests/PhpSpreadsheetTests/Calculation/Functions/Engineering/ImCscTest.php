<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

class ImCscTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerIMCSC
     *
     * @param mixed $expectedResult
     */
    public function testIMCSC($expectedResult, ...$args): void
    {
        $this->runComplexTestCase('IMCSC', $expectedResult, ...$args);
    }

    public function providerIMCSC(): array
    {
        return require 'tests/data/Calculation/Engineering/IMCSC.php';
    }

    /**
     * @dataProvider providerImCscArray
     */
    public function testImCscArray(array $expectedResult, string $complex): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=IMCSC({$complex})";
        $result = $calculation->_calculateFormulaValue($formula);
        // Avoid testing for excess precision
        foreach ($expectedResult as &$array) {
            foreach ($array as &$string) {
                $string = preg_replace('/(\\d{8})\\d+/', '$1', $string);
            }
        }
        foreach ($result as &$array) {
            foreach ($array as &$string) {
                $string = preg_replace('/(\\d{8})\\d+/', '$1', $string);
            }
        }

        self::assertEquals($expectedResult, $result);
    }

    public function providerImCscArray(): array
    {
        return [
            'row/column vector' => [
                [
                    ['-0.13829327777622+0.087608481088326i', '0.1652836698551i', '0.13829327777622+0.087608481088326i'],
                    ['-0.62151801717043+0.30393100162843i', '0.85091812823932i', '0.62151801717043+0.30393100162843i'],
                    ['-0.62151801717043-0.30393100162843i', '-0.85091812823932i', '0.62151801717043-0.30393100162843i'],
                    ['-0.13829327777622-0.087608481088326i', '-0.1652836698551i', '0.13829327777622-0.087608481088326i'],
                ],
                '{"-1-2.5i", "-2.5i", "1-2.5i"; "-1-i", "-i", "1-i"; "-1+i", "i", "1+1"; "-1+2.5i", "+2.5i", "1+2.5i"}',
            ],
        ];
    }
}
