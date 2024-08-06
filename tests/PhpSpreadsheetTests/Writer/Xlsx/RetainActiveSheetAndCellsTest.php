<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Shared\File;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;
use PHPUnit\Framework\TestCase;

class RetainActiveSheetAndCellsTest extends TestCase
{
    public function testRetain(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet1 = $spreadsheet->getActiveSheet();
        $array = [
            [1, 2, 3, 4, 5],
            [11, 12, 13, 14, 15],
            [21, 22, 23, 24, 25],
            [31, 32, 33, 34, 35],
        ];
        $sheet1->fromArray($array);
        $sheet2 = $spreadsheet->createSheet();
        $sheet2->fromArray($array);
        $sheet3 = $spreadsheet->createSheet();
        $sheet3->fromArray($array);
        $sheet1->getStyle('A1')->getFont()->setName('Arial');
        $sheet2->getStyle('A1')->getFont()->setName('Arial');
        $sheet3->getStyle('A1')->getFont()->setName('Arial');
        $sheet1->getColumnDimension('A')->setAutoSize(true);
        $sheet2->getColumnDimension('A')->setAutoSize(true);
        $sheet3->getColumnDimension('A')->setAutoSize(true);
        $sheet1->setSelectedCells('B2');
        $sheet2->setSelectedCells('C3');
        $sheet3->setSelectedCells('D4');
        $spreadsheet->setActiveSheetIndex(1);

        $outfile = File::temporaryFilename();
        $writer = new XlsxWriter($spreadsheet);
        $writer->save($outfile);
        unlink($outfile);

        self::assertSame(1, $spreadsheet->getActiveSheetIndex());
        self::assertSame('B2', $spreadsheet->getSheet(0)->getSelectedCells());
        self::assertSame('C3', $spreadsheet->getSheet(1)->getSelectedCells());
        self::assertSame('D4', $spreadsheet->getSheet(2)->getSelectedCells());

        $spreadsheet->disconnectWorksheets();
    }
}
