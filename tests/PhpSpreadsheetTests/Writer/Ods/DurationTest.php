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
        $sheet->getCell('A1')->setValue(0.5);
        $sheet->getStyle('A1')->getNumberFormat()
            ->setFormatCode(NumberFormat::FORMAT_PERCENTAGE_0);
        $sheet->getCell('A2')->setValue(20);
        $sheet->getStyle('A2')->getNumberFormat()
            ->setFormatCode('$0.00');
        $sheet->getCell('A3')->setValue(20);
        $sheet->getStyle('A3')->getNumberFormat()
            ->setFormatCode('#.0');
        $sheet->getCell('A4')->setValue(46000);
        $sheet->getStyle('A4')->getNumberFormat()
            ->setFormatCode('yyyy-mm-dd');

        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, 'Ods');
        $spreadsheet->disconnectWorksheets();
        $rsheet = $reloadedSpreadsheet->getActiveSheet();
        self::assertSame('1:02:34', $rsheet->getCell('F2')->getFormattedValue());
        self::assertSame('-00:12:38', $rsheet->getCell('F3')->getFormattedValue());
        self::assertSame('50.0%', $rsheet->getCell('A1')->getFormattedValue());
        self::assertSame('$20.00 ', $rsheet->getCell('A2')->getFormattedValue());
        self::assertSame('20.0', $rsheet->getCell('A3')->getFormattedValue());
        self::assertSame('2025-12-09', $rsheet->getCell('A4')->getFormattedValue());
        $reloadedSpreadsheet->disconnectWorksheets();
    }
}
