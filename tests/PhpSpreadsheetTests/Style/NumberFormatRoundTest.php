<?php

namespace PhpOffice\PhpSpreadsheetTests\Style;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

class NumberFormatRoundTest extends TestCase
{
    public static function testRound(): void
    {
        // Inconsistent rounding due to letting sprintf do it rather than round.
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getStyle('A1:H2')->getNumberFormat()->setFormatCode('0');
        $sheet->getStyle('A3:H3')->getNumberFormat()->setFormatCode('0.0');
        $sheet->fromArray(
            [
                [-3.5, -2.5, -1.5, -0.5, 0.5, 1.5, 2.5, 3.5],
                [-3.1, -2.9, -1.4, -0.3, 0.7, 1.6, 2.4, 3.7],
                [-3.15, -2.85, -1.43, -0.87, 0.72, 1.60, 2.45, 3.75],
            ]
        );
        $expected = [
            [-4, -3, -2, -1, 1, 2, 3, 4],
            [-3, -3, -1, 0, 1, 2, 2, 4],
            [-3.2, -2.9, -1.4, -0.9, 0.7, 1.6, 2.5, 3.8],
        ];
        self::assertEquals($expected, $sheet->toArray());
        $spreadsheet->disconnectWorksheets();
    }
}
