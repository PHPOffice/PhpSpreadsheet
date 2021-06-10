<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;

class SeriesSumTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerSERIESSUM
     *
     * @param mixed $expectedResult
     * @param mixed $arg1
     * @param mixed $arg2
     * @param mixed $arg3
     */
    public function testSERIESSUM($expectedResult, $arg1, $arg2, $arg3, ...$args): void
    {
        $sheet = $this->getSheet();
        if ($arg1 !== null) {
            $sheet->getCell('C1')->setValue($arg1);
        }
        if ($arg2 !== null) {
            $sheet->getCell('C2')->setValue($arg2);
        }
        if ($arg3 !== null) {
            $sheet->getCell('C3')->setValue($arg3);
        }
        $row = 0;
        $aArgs = Functions::flattenArray($args);
        foreach ($aArgs as $arg) {
            ++$row;
            if ($arg !== null) {
                $sheet->getCell("A$row")->setValue($arg);
            }
        }
        $sheet->getCell('B1')->setValue("=SERIESSUM(C1, C2, C3, A1:A$row)");
        $result = $sheet->getCell('B1')->getCalculatedValue();
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerSERIESSUM(): array
    {
        return require 'tests/data/Calculation/MathTrig/SERIESSUM.php';
    }
}
