<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Ods;

use DateTime;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class MicrosecondsTest extends AbstractFunctional
{
    /**
     * Test save and load XLSX file for round-trip DateTime.
     * Ods Writer does not support date formats,
     * and Reader does not support styles, so this
     * test is slightly different than its Xls/Xlsx counterparts.
     */
    public function testIssue4476(): void
    {
        $date = '2020-10-21';
        $time = '14:55:31';
        $originalDateTime = new DateTime("{$date}T{$time}");
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', Date::dateTimeToExcel($originalDateTime));
        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, 'Ods');
        $spreadsheet->disconnectWorksheets();

        $rsheet = $reloadedSpreadsheet->getActiveSheet();
        $rsheet->getStyle('A1')
            ->getNumberFormat()
            ->setFormatCode('yyyy-mm-dd hh:mm:ss.000');
        /** @var float */
        $reread = $rsheet->getCell('A1')->getValue();
        $temp = Date::excelToDateTimeObject($reread)
            ->format('Y-m-d H:i:s.u');
        self::assertSame("{$date} {$time}.000000", $temp, 'round trip works with float value');
        $formatted = $rsheet->getCell('A1')->getFormattedValue();
        self::assertSame("{$date} {$time}.000", $formatted, 'round trip works with formatted value');
        /** @var float */
        $temp = Date::stringToExcel($formatted);
        $temp = Date::excelToDateTimeObject($temp)
            ->format('Y-m-d H:i:s.u');
        self::assertSame("{$date} {$time}.000000", $temp, 'round trip works using stringToExcel on formatted value');

        $reloadedSpreadsheet->disconnectWorksheets();
    }
}
