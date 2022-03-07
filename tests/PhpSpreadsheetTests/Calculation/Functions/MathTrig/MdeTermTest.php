<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

class MdeTermTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerMDETERM
     *
     * @param mixed $expectedResult
     * @param mixed $matrix expect a matrix
     */
    public function testMDETERM2($expectedResult, $matrix): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        if (is_array($matrix)) {
            $sheet->fromArray($matrix, null, 'A1', true);
            $maxCol = $sheet->getHighestColumn();
            $maxRow = $sheet->getHighestRow();
            $sheet->getCell('Z1')->setValue("=MDETERM(A1:$maxCol$maxRow)");
        } else {
            $sheet->getCell('Z1')->setValue("=MDETERM($matrix)");
        }
        $result = $sheet->getCell('Z1')->getCalculatedValue();
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerMDETERM(): array
    {
        return require 'tests/data/Calculation/MathTrig/MDETERM.php';
    }
}
