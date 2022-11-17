<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

class PDurationTest extends TestCase
{
    /**
     * @dataProvider providerPDURATION
     *
     * @param mixed $expectedResult
     */
    public function testPDURATION($expectedResult, array $args): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray($args, null, 'A1', true);
        if (count($args) === 0) {
            $formula = '=PDURATION()';
        } elseif (count($args) === 1) {
            $formula = '=PDURATION(A1)';
        } elseif (count($args) === 2) {
            $formula = '=PDURATION(A1, B1)';
        } else {
            $formula = '=PDURATION(A1, B1, C1)';
        }
        $sheet->getCell('A2')->setValue($formula);
        $result = $sheet->getCell('A2')->getCalculatedValue();
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
        $spreadsheet->disconnectWorksheets();
    }

    public function providerPDURATION(): array
    {
        return require 'tests/data/Calculation/Financial/PDURATION.php';
    }
}
