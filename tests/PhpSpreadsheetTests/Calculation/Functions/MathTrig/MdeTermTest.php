<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

class MdeTermTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerMDETERM
     */
    public function testMDETERM2(float|int|string $expectedResult, array|int|float|string $matrix): void
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

    public static function providerMDETERM(): array
    {
        return require 'tests/data/Calculation/MathTrig/MDETERM.php';
    }
}
