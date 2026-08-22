<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\LookupRef;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

/**
 * Many of these tests are derived from
 * https://exceljet.net/functions/hstack-function.
 */
class HStackTest extends AllSetupTeardown
{
    public static function testHstack1(): void
    {
        $spreadsheet = new Spreadsheet();
        Calculation::getInstance($spreadsheet)
            ->setInstanceArrayReturnType(
                Calculation::RETURN_ARRAY_AS_ARRAY
            );
        $sheet = $spreadsheet->getActiveSheet();
        $b4tof14 = [
            ['A', null, 'B', null, 'C'],
            [12, null, 7, null, 12],
            [9, null, 13, null, 10],
            [10, null, 5, null, 11],
            [11, null, 13, null, 6],
            [8, null, 12, null, 7],
            [12, null, 11, null, 15],
            [9, null, 10, null, 6],
            [10, null, 15, null, 5],
            [11, null, 9, null, 14],
            [6, null, 13, null, 11],
        ];
        $sheet->fromArray($b4tof14, null, 'B4', true);
        $sheet->setCellValue('H4', '=HSTACK(B4:B14,D4:D14,F4:F14)');
        $expected = [
            ['A', 'B', 'C'],
            [12, 7, 12],
            [9, 13, 10],
            [10, 5, 11],
            [11, 13, 6],
            [8, 12, 7],
            [12, 11, 15],
            [9, 10, 6],
            [10, 15, 5],
            [11, 9, 14],
            [6, 13, 11],
        ];
        self::assertSame($expected, $sheet->getCell('H4')->getCalculatedValue());

        $sheet->setCellValue('K4', '=HSTACK(B4:B14,D4:D12,F4:F14)');
        $expected = [
            ['A', 'B', 'C'],
            [12, 7, 12],
            [9, 13, 10],
            [10, 5, 11],
            [11, 13, 6],
            [8, 12, 7],
            [12, 11, 15],
            [9, 10, 6],
            [10, 15, 5],
            [11, '#N/A', 14],
            [6, '#N/A', 11],
        ];
        self::assertSame($expected, $sheet->getCell('K4')->getCalculatedValue(), 'one short column');

        $sheet->setCellValue('R4', '=IFERROR(HSTACK(B4:B14,D4:D12,F4:F14),"")');
        $expected = [
            ['A', 'B', 'C'],
            [12, 7, 12],
            [9, 13, 10],
            [10, 5, 11],
            [11, 13, 6],
            [8, 12, 7],
            [12, 11, 15],
            [9, 10, 6],
            [10, 15, 5],
            [11, '', 14],
            [6, '', 11],
        ];
        self::assertSame($expected, $sheet->getCell('R4')->getCalculatedValue(), 'one short column with null string instead of N/A');

        $sheet->setCellValue('N4', '=HSTACK(B5,H6:J6)');
        $expected = [
            [12, 9, 13, 10],
        ];
        self::assertSame($expected, $sheet->getCell('N4')->getCalculatedValue(), 'one single-cell arg');

        $spreadsheet->disconnectWorksheets();
    }
}
