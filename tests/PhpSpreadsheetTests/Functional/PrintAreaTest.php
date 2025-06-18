<?php

namespace PhpOffice\PhpSpreadsheetTests\Functional;

use PhpOffice\PhpSpreadsheet\Reader\BaseReader;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class PrintAreaTest extends AbstractFunctional
{
    public function providerFormats()
    {
        return [
            ['Xls'],
            ['Xlsx'],
        ];
    }

    /**
     * @dataProvider providerFormats
     *
     * @param string $format
     */
    public function testPageSetup($format)
    {
        // Create new workbook with 3 sheets and different print areas
        $spreadsheet = new Spreadsheet();
        $worksheet1 = $spreadsheet->getActiveSheet()->setTitle('Sheet 1');
        $worksheet1->getPageSetup()->setPrintArea('A1:B1');

        for ($i = 2; $i < 4; ++$i) {
            $sheet = $spreadsheet->createSheet()->setTitle("Sheet $i");
            $sheet->getPageSetup()->setPrintArea("A$i:B$i");
        }

        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, $format, function (BaseReader $reader) {
            $reader->setLoadSheetsOnly(['Sheet 1', 'Sheet 3']);
        });

        $actual1 = $reloadedSpreadsheet->getSheetByName('Sheet 1')->getPageSetup()->getPrintArea();
        $actual3 = $reloadedSpreadsheet->getSheetByName('Sheet 3')->getPageSetup()->getPrintArea();
        self::assertSame('A1:B1', $actual1, 'should be able to write and read normal page setup');
        self::assertSame('A3:B3', $actual3, 'should be able to write and read page setup even when skipping sheets');
    }
}
