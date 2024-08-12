<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class Issue3534Test extends \PHPUnit\Framework\TestCase
{
    private static string $testbook = 'tests/data/Reader/XLSX/issue.3534.xlsx';

    public function testReadColumnStyles(): void
    {
        $reader = new XlsxReader();
        $spreadsheet = $reader->load(self::$testbook);
        $sheet = $spreadsheet->getActiveSheet();

        self::assertSame(['B4', 'C4'], $sheet->getCellCollection()->getCoordinates(), 'explicitly defined despite no value because differ from row style');
        self::assertSame(1, $sheet->getColumnDimension('A')->getXfIndex());
        self::assertNull($sheet->getRowDimension(1)->getXfIndex());

        $sheet->getCell('A1')->setValue('a1');
        self::assertSame(['B4', 'C4', 'A1'], $sheet->getCellCollection()->getCoordinates());
        self::assertSame(1, $sheet->getCell('A1')->getXfIndex(), 'no row style so apply column style');

        $sheet->getCell('A4')->setValue('a4');
        $sheet->getCell('C1')->setValue('c1');
        self::assertSame('ED7D31', $sheet->getStyle('A1')->getFill()->getStartColor()->getRgb(), 'no row style so apply column style');
        self::assertSame('FFC000', $sheet->getStyle('A4')->getFill()->getStartColor()->getRgb(), 'style for row is applied');
        self::assertSame('9DC3E6', $sheet->getStyle('C1')->getFill()->getStartColor()->getRgb(), 'no row style so apply column style');
        self::assertSame('9DC3E6', $sheet->getStyle('C4')->getFill()->getStartColor()->getRgb(), 'style was already set in xml');
        self::assertSame(Fill::FILL_NONE, $sheet->getStyle('B4')->getFill()->getFillType(), 'style implicitly default in xml');
        $spreadsheet->disconnectWorksheets();
    }
}
