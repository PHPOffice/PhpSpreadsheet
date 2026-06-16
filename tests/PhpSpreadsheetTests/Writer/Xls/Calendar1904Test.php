<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Xls;

use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class Calendar1904Test extends AbstractFunctional
{
    public function testCalendar1904(): void
    {
        $spreadsheet = new Spreadsheet();
        $spreadsheet->setExcelCalendar(Date::CALENDAR_MAC_1904);
        $worksheet = $spreadsheet->getActiveSheet();
        $worksheet->getCell('A1')->setValue('=DATE(1904,1,1)');
        $worksheet->getStyle('A1')->getNumberFormat()
            ->setFormatCode('yyyy-mm-dd');
        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, 'Xls');
        $spreadsheet->disconnectWorksheets();
        $rsheet = $reloadedSpreadsheet->getActiveSheet();
        self::assertSame(0.0, $rsheet->getCell('A1')->getCalculatedValue());
        self::assertSame('1904-01-01', $rsheet->getCell('A1')->getFormattedValue());
        self::assertSame(Date::CALENDAR_MAC_1904, $reloadedSpreadsheet->getExcelCalendar());
        $reloadedSpreadsheet->disconnectWorksheets();
    }
}
