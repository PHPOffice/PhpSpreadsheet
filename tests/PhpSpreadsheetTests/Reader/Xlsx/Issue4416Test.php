<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PHPUnit\Framework\TestCase;

class Issue4416Test extends TestCase
{
    private static string $file = 'tests/data/Reader/XLSX/issue.4416.smallauto.xlsx';

    public function testNoFilter(): void
    {
        $file = self::$file;
        $reader = new XlsxReader();
        $spreadsheet = $reader->load($file);
        $sheet = $spreadsheet->getActiveSheet();
        self::assertEqualsWithDelta(
            16.5430,
            $sheet->getColumnDimension('A')->getWidth(),
            1E-4
        );
        self::assertEqualsWithDelta(
            6.0,
            $sheet->getColumnDimension('B')->getWidth(),
            1E-4
        );
        self::assertEqualsWithDelta(
            11.3633,
            $sheet->getColumnDimension('C')->getWidth(),
            1E-4
        );
        self::assertEqualsWithDelta(
            41.0898,
            $sheet->getColumnDimension('D')->getWidth(),
            1E-4
        );
        self::assertEqualsWithDelta(
            28.5,
            $sheet->getRowDimension(6)->getRowHeight(),
            1E-4
        );
        $spreadsheet->disconnectWorksheets();
    }

    public function testWithFilter(): void
    {
        $file = self::$file;
        $reader = new XlsxReader();
        $reader->setReadFilter(new Issue4416Filter());
        $spreadsheet = $reader->load($file);
        $sheet = $spreadsheet->getActiveSheet();
        self::assertEqualsWithDelta(
            16.5430,
            $sheet->getColumnDimension('A')->getWidth(),
            1E-4
        );
        self::assertEqualsWithDelta(
            6.0,
            $sheet->getColumnDimension('B')->getWidth(),
            1E-4
        );
        self::assertEqualsWithDelta(
            11.3633,
            $sheet->getColumnDimension('C')->getWidth(),
            1E-4
        );
        self::assertEqualsWithDelta(
            41.0898,
            $sheet->getColumnDimension('D')->getWidth(),
            1E-4
        );
        self::assertEquals(
            -1,
            $sheet->getRowDimension(6)->getRowHeight(),
            'row has been filtered away'
        );
        $spreadsheet->disconnectWorksheets();
    }
}
