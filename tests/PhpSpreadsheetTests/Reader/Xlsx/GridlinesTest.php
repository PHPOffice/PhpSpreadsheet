<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class GridlinesTest extends AbstractFunctional
{
    /**
     * @dataProvider loadDataProvider
     */
    public function testGridlines(bool $display, bool $print): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet1 = $spreadsheet->getActiveSheet();
        $sheet2 = $spreadsheet->createSheet();
        $sheet2->setTitle('deliberatelyblank');
        $sheet1->setShowGridlines($display);
        $sheet1->setPrintGridlines($print);
        $sheet1->fromArray(
            [
                [1, 2, 3],
                [4, 5, 6],
                [7, 8, 9],
            ]
        );
        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, 'Xlsx');
        $spreadsheet->disconnectWorksheets();
        $rsheet1 = $reloadedSpreadsheet->getSheet(0);
        $rsheet2 = $reloadedSpreadsheet->getSheet(1);
        self::assertSame($display, $rsheet1->getShowGridlines());
        self::assertSame($print, $rsheet1->getPrintGridlines());
        self::assertTrue($rsheet2->getShowGridlines());
        self::assertFalse($rsheet2->getPrintGridlines());
        $reloadedSpreadsheet->disconnectWorksheets();
    }

    public static function loadDataProvider(): array
    {
        return [
            [true, true],
            [true, false],
            [false, true],
            [false, false],
        ];
    }
}
