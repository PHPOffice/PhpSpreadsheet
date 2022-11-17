<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

class NPerTest extends TestCase
{
    /**
     * @dataProvider providerNPER
     *
     * @param mixed $expectedResult
     */
    public function testNPER($expectedResult, array $args): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray($args, null, 'A1', true);
        if (count($args) === 0) {
            $formula = '=NPER()';
        } elseif (count($args) === 1) {
            $formula = '=NPER(A1)';
        } elseif (count($args) === 2) {
            $formula = '=NPER(A1, B1)';
        } elseif (count($args) === 3) {
            $formula = '=NPER(A1, B1, C1)';
        } elseif (count($args) === 4) {
            $formula = '=NPER(A1, B1, C1, D1)';
        } else {
            $formula = '=NPER(A1, B1, C1, D1, E1)';
        }
        $sheet->getCell('A2')->setValue($formula);
        $result = $sheet->getCell('A2')->getCalculatedValue();
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
        $spreadsheet->disconnectWorksheets();
    }

    public function providerNPER(): array
    {
        return require 'tests/data/Calculation/Financial/NPER.php';
    }
}
