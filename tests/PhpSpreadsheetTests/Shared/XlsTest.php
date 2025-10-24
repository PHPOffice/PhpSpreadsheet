<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Shared;

use PhpOffice\PhpSpreadsheet\Shared\Xls as SharedXls;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

class XlsTest extends TestCase
{
    public function testSizes(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray([[1, 2], [3, 4]]);
        $sheet->getColumnDimension('B')->setVisible(false);
        $sheet->getRowDimension(2)->setVisible(false);
        self::assertSame(64, SharedXls::sizeCol($sheet, 'A'));
        self::assertSame(0, SharedXls::sizeCol($sheet, 'B'));
        self::assertSame(20, SharedXls::sizeRow($sheet, 1));
        self::assertSame(0, SharedXls::sizeRow($sheet, 2));
        $spreadsheet->disconnectWorksheets();
    }
}
