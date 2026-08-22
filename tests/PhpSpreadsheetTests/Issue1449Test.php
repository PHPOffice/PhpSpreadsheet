<?php

namespace PhpOffice\PhpSpreadsheetTests;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

class Issue1449Test extends TestCase
{
    protected bool $skipTests = true;

    public function testDeleteColumns(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet1 = $spreadsheet->getActiveSheet();
        $sheet2 = $spreadsheet->createSheet();
        $sheet1->setTitle('Sheet1');
        $sheet2->setTitle('Sheet2');
        $sheet1->fromArray(
            [
                [3, 1, 2, 33, 1, 10, 20, 30, 40],
                [4, 2, 3, 23, 2, 10, 20, 30, 40],
                [5, 3, 4, 1, 3, 10, 20, 30, 40],
                [6, 4, 6, 4, 3, 10, 20, 30, 40],
                [7, 6, 6, 2, 2, 10, 20, 30, 40],
            ],
            null,
            'C1',
            true
        );
        $sheet1->getCell('A1')->setValue('=SUM(C4:F7)');
        $sheet2->getCell('A1')->setValue('=SUM(Sheet1!C3:G5)');
        $sheet1->removeColumn('F', 4);
        self::assertSame('=SUM(C4:E7)', $sheet1->getCell('A1')->getValue());
        if (!$this->skipTests) {
            // References on another sheet not working yet.
            self::assertSame('=Sheet1!SUM(C3:E5)', $sheet2->getCell('A1')->getValue());
        }
        $spreadsheet->disconnectWorksheets();
    }

    public function testDeleteRows(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet1 = $spreadsheet->getActiveSheet();
        $sheet2 = $spreadsheet->createSheet();
        $sheet1->setTitle('Sheet1');
        $sheet2->setTitle('Sheet2');
        $sheet1->fromArray(
            [
                [3, 1, 2, 33, 1, 10, 20, 30, 40],
                [4, 2, 3, 23, 2, 10, 20, 30, 40],
                [5, 3, 4, 1, 3, 10, 20, 30, 40],
                [6, 4, 6, 4, 3, 10, 20, 30, 40],
                [7, 6, 6, 2, 2, 10, 20, 30, 40],
            ],
            null,
            'C1',
            true
        );
        $sheet1->getCell('A1')->setValue('=SUM(C4:F7)');
        $sheet2->getCell('A1')->setValue('=SUM(Sheet1!C3:G5)');
        $sheet1->removeRow(4, 2);
        self::assertSame('=SUM(C4:F5)', $sheet1->getCell('A1')->getValue());
        if (!$this->skipTests) {
            // References on another sheet not working yet.
            self::assertSame('=Sheet1!SUM(C3:G3)', $sheet2->getCell('A1')->getValue());
        }
        $spreadsheet->disconnectWorksheets();
    }
}
