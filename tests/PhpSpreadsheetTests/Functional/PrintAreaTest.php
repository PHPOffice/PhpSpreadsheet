<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Functional;

use PhpOffice\PhpSpreadsheet\Reader\BaseReader;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class PrintAreaTest extends AbstractFunctional
{
    public static function providerFormats(): array
    {
        return [
            ['Xls'],
            ['Xlsx'],
        ];
    }

    /**
     * @dataProvider providerFormats
     */
    public function testPageSetup(string $format): void
    {
        // Create new workbook with 6 sheets and different print areas
        $spreadsheet = new Spreadsheet();
        $worksheet1 = $spreadsheet->getActiveSheet()->setTitle('Sheet 1');
        $worksheet1->getPageSetup()->setPrintArea('A1:B1');

        for ($i = 2; $i < 4; ++$i) {
            $sheet = $spreadsheet->createSheet()->setTitle("Sheet $i");
            $sheet->getPageSetup()->setPrintArea("A$i:B$i");
        }

        $worksheet4 = $spreadsheet->createSheet()->setTitle('Sheet 4');
        $worksheet4->getPageSetup()->setPrintArea('A4:B4,D1:E4');
        $worksheet5 = $spreadsheet->createSheet()->setTitle('Sheet 5');
        $worksheet5->getPageSetup()->addPrintAreaByColumnAndRow(1, 1, 10, 10, 1);
        $worksheet6 = $spreadsheet->createSheet()->setTitle('Sheet 6');
        $worksheet6->getPageSetup()->addPrintAreaByColumnAndRow(1, 1, 10, 10, 1);
        $worksheet6->getPageSetup()->addPrintAreaByColumnAndRow(12, 1, 12, 10, 1);

        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, $format, function (BaseReader $reader): void {
            $reader->setLoadSheetsOnly(['Sheet 1', 'Sheet 3', 'Sheet 4', 'Sheet 5', 'Sheet 6']);
        });

        $actual1 = self::getPrintArea($reloadedSpreadsheet, 'Sheet 1');
        $actual3 = self::getPrintArea($reloadedSpreadsheet, 'Sheet 3');
        $actual4 = self::getPrintArea($reloadedSpreadsheet, 'Sheet 4');
        $actual5 = self::getPrintArea($reloadedSpreadsheet, 'Sheet 5');
        $actual6 = self::getPrintArea($reloadedSpreadsheet, 'Sheet 6');
        self::assertSame('A1:B1', $actual1, 'should be able to write and read normal page setup');
        self::assertSame('A3:B3', $actual3, 'should be able to write and read page setup even when skipping sheets');
        self::assertSame('A4:B4,D1:E4', $actual4, 'should be able to write and read page setup with multiple print areas');
        self::assertSame('A1:J10', $actual5, 'add by column and row');
        self::assertSame('A1:J10,L1:L10', $actual6, 'multiple add by column and row');
    }

    private static function getPrintArea(Spreadsheet $spreadsheet, string $name): string
    {
        $sheet = $spreadsheet->getSheetByNameOrThrow($name);

        return $sheet->getPageSetup()->getPrintArea();
    }
}
