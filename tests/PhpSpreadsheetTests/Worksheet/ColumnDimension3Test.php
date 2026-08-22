<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Worksheet;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class ColumnDimension3Test extends AbstractFunctional
{
    public function testXlsxDefault(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getCell('B1')->setValue('hello');
        $sheet->getCell('C1')->setValue(2);
        $sheet->getCell('A1')->setValue('=REPT("ABCDEFGHIJKLMNOPQRS*",13)');
        $sheet->getStyle('A1')->getFont()
            ->setSize(11)
            ->setName('Courier New');
        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, 'Xlsx');
        $spreadsheet->disconnectWorksheets();
        $sheet = $reloadedSpreadsheet->getActiveSheet();
        self::assertGreaterThan(255.0, $sheet->getColumnDimension('A')->getWidth());
        self::assertLessThan(255.0, $sheet->getColumnDimension('B')->getWidth());
        self::assertSame(-1.0, $sheet->getColumnDimension('C')->getWidth());
        $reloadedSpreadsheet->disconnectWorksheets();
    }

    public function testXlsxRestrict(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getCell('B1')->setValue('hello');
        $sheet->getCell('C1')->setValue(2);
        $sheet->getCell('A1')->setValue('=REPT("ABCDEFGHIJKLMNOPQRS*",13)');
        $sheet->getStyle('A1')->getFont()
            ->setSize(11)
            ->setName('Courier New');
        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, 'Xlsx', null, [self::class, 'restrictWidth']);
        $spreadsheet->disconnectWorksheets();
        $sheet = $reloadedSpreadsheet->getActiveSheet();
        self::assertSame(255.0, $sheet->getColumnDimension('A')->getWidth());
        self::assertLessThan(255.0, $sheet->getColumnDimension('B')->getWidth());
        self::assertSame(-1.0, $sheet->getColumnDimension('C')->getWidth());
        $reloadedSpreadsheet->disconnectWorksheets();
    }

    public function testXls(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getCell('B1')->setValue('hello');
        $sheet->getCell('C1')->setValue(2);
        $sheet->getCell('A1')->setValue('=REPT("ABCDEFGHIJKLMNOPQRS*",13)');
        $sheet->getStyle('A1')->getFont()
            ->setSize(11)
            ->setName('Courier New');
        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, 'Xls');
        $spreadsheet->disconnectWorksheets();
        $sheet = $reloadedSpreadsheet->getActiveSheet();
        self::assertSame(255.0, $sheet->getColumnDimension('A')->getWidth());
        self::assertLessThan(255.0, $sheet->getColumnDimension('B')->getWidth());
        self::assertLessThan(255.0, $sheet->getColumnDimension('C')->getWidth());
        $reloadedSpreadsheet->disconnectWorksheets();
    }

    public static function restrictWidth(XlsxWriter $writer): void
    {
        $writer->setRestrictMaxColumnWidth(true);
    }
}
