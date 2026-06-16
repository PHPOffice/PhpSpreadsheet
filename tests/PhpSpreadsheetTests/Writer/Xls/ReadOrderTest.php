<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Xls;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class ReadOrderTest extends AbstractFunctional
{
    public function testBooleanLiteral(): void
    {
        // Issue 850 - Xls Reader/Writer didn't support Alignment ReadOrder
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', '1-' . 'منصور حسين الناصر');
        $sheet->setCellValue('A2', '1-' . 'منصور حسين الناصر');
        $sheet->setCellValue('A3', '1-' . 'منصور حسين الناصر');
        $sheet->getStyle('A1')
            ->getAlignment()->setReadOrder(Alignment::READORDER_RTL);
        $sheet->getStyle('A2')
            ->getAlignment()->setReadOrder(Alignment::READORDER_LTR);

        $sheet->setCellValue('A5', 'hello');
        $spreadsheet->getActiveSheet()->getStyle('A5')
            ->getAlignment()->setIndent(2);

        $robj = $this->writeAndReload($spreadsheet, 'Xls');
        $spreadsheet->disconnectWorksheets();
        $sheet0 = $robj->setActiveSheetIndex(0);
        self::assertSame(
            Alignment::READORDER_RTL,
            $sheet0->getStyle('A1')->getAlignment()->getReadOrder()
        );
        self::assertSame(
            Alignment::READORDER_LTR,
            $sheet0->getStyle('A2')->getAlignment()->getReadOrder()
        );
        self::assertSame(
            Alignment::READORDER_CONTEXT,
            $sheet0->getStyle('A3')->getAlignment()->getReadOrder()
        );
        self::assertSame(
            2,
            $sheet0->getStyle('A5')->getAlignment()->getIndent()
        );
        $robj->disconnectWorksheets();
    }
}
