<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;

class SumProductTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerSUMPRODUCT
     *
     * @param mixed $expectedResult
     */
    public function testSUMPRODUCT($expectedResult, ...$args): void
    {
        $sheet = $this->getSheet();
        $row = 0;
        $arrayArg = '';
        foreach ($args as $arr) {
            $arr2 = Functions::flattenArray($arr);
            $startRow = 0;
            foreach ($arr2 as $arr3) {
                ++$row;
                if (!$startRow) {
                    $startRow = $row;
                }
                $sheet->getCell("A$row")->setValue($arr3);
            }
            $arrayArg .= "A$startRow:A$row,";
        }
        $arrayArg = substr($arrayArg, 0, -1); // strip trailing comma
        $sheet->getCell('B1')->setValue("=SUMPRODUCT($arrayArg)");
        $result = $sheet->getCell('B1')->getCalculatedValue();
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerSUMPRODUCT(): array
    {
        return require 'tests/data/Calculation/MathTrig/SUMPRODUCT.php';
    }
}
