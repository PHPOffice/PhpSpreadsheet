<?php

namespace PhpOffice\PhpSpreadsheetTests\Reader\Ods;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;
use PHPUnit\Framework\Attributes\DataProvider;

class OldCalculatedTest extends AbstractFunctional
{
    #[DataProvider('oldCalcProvider')]
    public function testOldCalc(
        mixed $expected,
        string $formula,
        string $style = '',
    ): void {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', $formula);
        if ($style !== '') {
            $sheet->getStyle('A1')->getNumberFormat()
                ->setFormatCode($style);
        }
        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, 'Ods');
        $spreadsheet->disconnectWorksheets();
        $rsheet = $reloadedSpreadsheet->getActiveSheet();
        $oldCalc = $rsheet->getCell('A1')->getOldCalculatedValue();
        if (is_float($expected) && is_float($oldCalc)) {
            self::assertEqualsWithDelta($expected, $oldCalc, 1E-8);
        } else {
            self::assertSame($expected, $oldCalc);
        }
        $reloadedSpreadsheet->disconnectWorksheets();
    }

    public static function oldCalcProvider(): array
    {
        return [
            'calculation error' => ['#CALC!', '=SUM()'], // moved from Writer\Ods\BadFormulaTest
            'date' => [43861.0, '=DATE(2020, 1, 31)', 'yyyy-mm-dd'],
            'time' => [0.6392361111111111, '=TIME(15,20,30)', 'hh:mm:ss'],
            'currency' => [1.25, '=0.5+0.75', '$0.00'],
            'percent' => [0.75, '=0.5+0.25', '0.00%'],
            'integer' => [3, '=1+2'],
            'float' => [3.5, '=1+2.5'],
            'bool' => [true, '=TRUE()'],
        ];
    }
}
