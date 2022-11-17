<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

class PvTest extends TestCase
{
    /**
     * @dataProvider providerPV
     *
     * @param mixed $expectedResult
     */
    public function testPV($expectedResult, array $args): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray($args, null, 'A1', true);
        if (count($args) === 0) {
            $formula = '=PV()';
        } elseif (count($args) === 1) {
            $formula = '=PV(A1)';
        } elseif (count($args) === 2) {
            $formula = '=PV(A1, B1)';
        } elseif (count($args) === 3) {
            $formula = '=PV(A1, B1, C1)';
        } elseif (count($args) === 4) {
            $formula = '=PV(A1, B1, C1, D1)';
        } else {
            $formula = '=PV(A1, B1, C1, D1, E1)';
        }
        $sheet->getCell('A2')->setValue($formula);
        $result = $sheet->getCell('A2')->getCalculatedValue();
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
        $spreadsheet->disconnectWorksheets();
    }

    public function providerPV(): array
    {
        return require 'tests/data/Calculation/Financial/PV.php';
    }
}
