<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Xls;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class Issue4584Test extends AbstractFunctional
{
    public function testWriteAndReadRowDimension(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getDefaultRowDimension()->setRowHeight(20);
        $sheet->setCellValue('A1', 'hello there world 1');
        $sheet->getStyle('A1')->getAlignment()->setWrapText(true);
        $sheet->getRowDimension(1)->setCustomFormat(true);
        $sheet->setCellValue('A2', 'hello there world 2');
        $sheet->setCellValue('A4', 'hello there world 4');
        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, 'Xls');
        $spreadsheet->disconnectWorksheets();

        $rsheet = $reloadedSpreadsheet->getActiveSheet();
        self::assertSame(20.0, $rsheet->getDefaultRowDimension()->getRowHeight());
        $row1 = $rsheet->getRowDimension(1);
        self::assertTrue($row1->getCustomFormat());
        self::assertSame(-1.0, $row1->getRowHeight());
        $row2 = $rsheet->getRowDimension(2);
        self::assertFalse($row2->getCustomFormat());
        self::assertSame(-1.0, $row2->getRowHeight());
        $row4 = $rsheet->getRowDimension(4);
        self::assertFalse($row4->getCustomFormat());
        self::assertSame(-1.0, $row4->getRowHeight());
        $reloadedSpreadsheet->disconnectWorksheets();
    }
}
