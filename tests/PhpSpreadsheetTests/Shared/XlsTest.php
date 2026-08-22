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
        self::assertNull(SharedXls::oneAnchor2twoAnchor($sheet, 'B1', 0, 0, 100, 100));
        self::assertNull(SharedXls::oneAnchor2twoAnchor($sheet, 'A2', 0, 0, 100, 100));
        $expected = [
            'startCoordinates' => 'D9',
            'startOffsetX' => 0,
            'startOffsetY' => 0,
            'endCoordinates' => 'E13',
            'endOffsetX' => 576.0,
            'endOffsetY' => 256,
        ];
        self::assertSame($expected, SharedXls::oneAnchor2twoAnchor($sheet, 'D9', 0, 0, 100, 100));
        $spreadsheet->disconnectWorksheets();
    }
}
