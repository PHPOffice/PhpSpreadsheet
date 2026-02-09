<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Ods;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class DurationTest extends AbstractFunctional
{
    public function testDuration(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getCell('E1')->setValue('=TIMEVALUE("8:15:01")');
        $sheet->getCell('E2')->setValue('=TIMEVALUE("9:17:35")');
        $sheet->getCell('E3')->setValue('=TIMEVALUE("8:02:23")');
        $sheet->getCell('F2')->setValue('=E2-E1');
        $sheet->getCell('F3')->setValue('=E3-E1');
        $sheet->getStyle('E1:F3')->getNumberFormat()
            ->setFormatCode(NumberFormat::FORMAT_DATE_TIME_INTERVAL);

        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, 'Ods');
        $spreadsheet->disconnectWorksheets();
        $rsheet = $reloadedSpreadsheet->getActiveSheet();
        self::assertSame('1:02:34', $rsheet->getCell('F2')->getFormattedValue());
        self::assertSame('-00:12:38', $rsheet->getCell('F3')->getFormattedValue());
        $reloadedSpreadsheet->disconnectWorksheets();
    }
}
