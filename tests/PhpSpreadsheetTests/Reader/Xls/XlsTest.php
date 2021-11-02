<?php

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xls;

use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PhpOffice\PhpSpreadsheet\Shared\CodePage;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class XlsTest extends AbstractFunctional
{
    /**
     * Test load Xls file.
     */
    public function testLoadXlsSample(): void
    {
        $filename = 'tests/data/Reader/XLS/sample.xls';
        $reader = new Xls();
        $spreadsheet = $reader->load($filename);
        self::assertEquals('Title', $spreadsheet->getSheet(0)->getCell('A1')->getValue());
        $spreadsheet->disconnectWorksheets();
    }

    /**
     * Test load Xls file with invalid xfIndex.
     */
    public function testLoadXlsBug1505(): void
    {
        $filename = 'tests/data/Reader/XLS/bug1505.xls';
        $reader = new Xls();
        $spreadsheet = $reader->load($filename);
        $sheet = $spreadsheet->getActiveSheet();
        $col = $sheet->getHighestColumn();
        $row = $sheet->getHighestRow();

        $newspreadsheet = $this->writeAndReload($spreadsheet, 'Xlsx');
        $newsheet = $newspreadsheet->getActiveSheet();
        $newcol = $newsheet->getHighestColumn();
        $newrow = $newsheet->getHighestRow();

        self::assertEquals($spreadsheet->getSheetCount(), $newspreadsheet->getSheetCount());
        self::assertEquals($sheet->getTitle(), $newsheet->getTitle());
        self::assertEquals($sheet->getColumnDimensions(), $newsheet->getColumnDimensions());
        self::assertEquals($col, $newcol);
        self::assertEquals($row, $newrow);
        self::assertEquals($sheet->getCell('A1')->getFormattedValue(), $newsheet->getCell('A1')->getFormattedValue());
        self::assertEquals($sheet->getCell("$col$row")->getFormattedValue(), $newsheet->getCell("$col$row")->getFormattedValue());
        $spreadsheet->disconnectWorksheets();
        $newspreadsheet->disconnectWorksheets();
    }

    /**
     * Test load Xls file with invalid length in SST map.
     */
    public function testLoadXlsBug1592(): void
    {
        $filename = 'tests/data/Reader/XLS/bug1592.xls';
        $reader = new Xls();
        // When no fix applied, spreadsheet is not loaded
        $spreadsheet = $reader->load($filename);
        $sheet = $spreadsheet->getActiveSheet();
        $col = $sheet->getHighestColumn();
        $row = $sheet->getHighestRow();

        $newspreadsheet = $this->writeAndReload($spreadsheet, 'Xlsx');
        $newsheet = $newspreadsheet->getActiveSheet();
        $newcol = $newsheet->getHighestColumn();
        $newrow = $newsheet->getHighestRow();

        self::assertEquals($spreadsheet->getSheetCount(), $newspreadsheet->getSheetCount());
        self::assertEquals($sheet->getTitle(), $newsheet->getTitle());
        self::assertEquals($sheet->getColumnDimensions(), $newsheet->getColumnDimensions());
        self::assertEquals($col, $newcol);
        self::assertEquals($row, $newrow);

        $rowIterator = $sheet->getRowIterator();

        foreach ($rowIterator as $row) {
            foreach ($row->getCellIterator() as $cellx) {
                /** @var Cell */
                $cell = $cellx;
                $valOld = $cell->getFormattedValue();
                $valNew = $newsheet->getCell($cell->getCoordinate())->getFormattedValue();
                self::assertEquals($valOld, $valNew);
            }
        }
        $spreadsheet->disconnectWorksheets();
        $newspreadsheet->disconnectWorksheets();
    }

    /**
     * Test load Xls file with MACCENTRALEUROPE encoding, which is implemented
     * as MAC-CENTRALEUROPE on some systems. Issue #549.
     */
    public function testLoadMacCentralEurope(): void
    {
        $codePages = CodePage::getEncodings();
        self::assertIsArray($codePages[10029]);
        $filename = 'tests/data/Reader/XLS/maccentraleurope.xls';
        $reader = new Xls();
        // When no fix applied, spreadsheet fails to load on some systems
        $spreadsheet = $reader->load($filename);
        $sheet = $spreadsheet->getActiveSheet();
        self::assertSame('Ładowność', $sheet->getCell('I1')->getValue());
        $spreadsheet->disconnectWorksheets();
    }

    /**
     * First test changes array entry in CodePage.
     * This test confirms new that new entry is okay.
     */
    public function testLoadMacCentralEurope2(): void
    {
        $codePages = CodePage::getEncodings();
        self::assertIsString($codePages[10029]);
        $filename = 'tests/data/Reader/XLS/maccentraleurope.xls';
        $reader = new Xls();
        // When no fix applied, spreadsheet fails to load on some systems
        $spreadsheet = $reader->load($filename);
        $sheet = $spreadsheet->getActiveSheet();
        self::assertSame('Ładowność', $sheet->getCell('I1')->getValue());
        $spreadsheet->disconnectWorksheets();
    }

    public function testLoadXlsBug1114(): void
    {
        $filename = 'tests/data/Reader/XLS/bug1114.xls';
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filename);
        $sheet = $spreadsheet->getActiveSheet();
        self::assertSame(1148140800.0, $sheet->getCell('B2')->getValue());
        $spreadsheet->disconnectWorksheets();
    }
}
