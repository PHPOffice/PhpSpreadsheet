<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Cell\Hyperlink;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class HyperlinkTest extends AbstractFunctional
{
    public function testHyperlink(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet1 = $spreadsheet->getActiveSheet();
        $sheet1->setTitle('First');
        $sheet2 = $spreadsheet->createSheet();
        $sheet2->setTitle('Second');
        $sheet2->setCellValue('A100', 'other sheet');
        $sheet1->setCellValue('A100', 'this sheet');
        $sheet1->setCellValue('A1', '=HYPERLINK("#A100", "here")');
        $sheet1->setCellValue('A2', '=HYPERLINK("#Second!A100", "there")');
        $sheet1->setCellValue('A3', '=HYPERLINK("http://example.com", "external")');
        $sheet1->setCellValue('A4', 'gotoA101');
        $sheet1->getCell('A4')
            ->getHyperlink()
            ->setUrl('#A101');

        $robj = $this->writeAndReload($spreadsheet, 'Xlsx');
        $spreadsheet->disconnectWorksheets();
        $sheet0 = $robj->setActiveSheetIndex(0);
        self::assertSame('sheet://#A100', $sheet0->getCell('A1')->getHyperlink()->getUrl());
        self::assertSame('sheet://#Second!A100', $sheet0->getCell('A2')->getHyperlink()->getUrl());
        self::assertSame('http://example.com', $sheet0->getCell('A3')->getHyperlink()->getUrl());
        self::assertSame('sheet://#A101', $sheet0->getCell('A4')->getHyperlink()->getUrl());
        $robj->disconnectWorksheets();
    }

    public function testEmptyHyperlink(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'plain');
        $sheet->setHyperlink('A1', new Hyperlink());
        $sheet->setCellValue('A2', 'also plain');
        $sheet->setCellValue('A3', 'link');
        $sheet->getCell('A3')
            ->getHyperlink()
            ->setUrl('https://example.org');
        self::assertSame(
            ['A1', 'A3'],
            array_keys($sheet->getHyperlinkCollection())
        );
        $robj = $this->writeAndReload($spreadsheet, 'Xlsx');
        $spreadsheet->disconnectWorksheets();
        $sheet0 = $robj->getActiveSheet();
        self::assertSame(
            ['A3'],
            array_keys($sheet0->getHyperlinkCollection())
        );
        self::assertSame('https://example.org', $sheet0->getCell('A3')->getHyperlink()->getUrl());
        $robj->disconnectWorksheets();
    }
}
