<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Functional;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

class ReadBlankCellsTest extends AbstractFunctional
{
    public static function providerSheetFormat(): array
    {
        return [
            ['Xlsx', false],
            ['Xls', true],
            ['Ods', true],
            ['Csv', false],
            ['Html', false],
        ];
    }

    /**
     * Test load file with explicitly empty cells.
     */
    public function testLoadReadEmptyCells(): void
    {
        $filename = 'tests/data/Reader/XLSX/blankcell.xlsx';
        $reader = new Xlsx();
        $reloadedSpreadsheet = $reader->load($filename);
        self::assertTrue($reloadedSpreadsheet->getActiveSheet()->getCellCollection()->has('B2'));
        self::assertFalse($reloadedSpreadsheet->getActiveSheet()->getCellCollection()->has('C2'));
        self::assertTrue($reloadedSpreadsheet->getActiveSheet()->getCellCollection()->has('C3'));
        $reloadedSpreadsheet->disconnectWorksheets();
    }

    /**
     * Test load file ignoring empty cells.
     */
    public function testLoadDontReadEmptyCells(): void
    {
        $filename = 'tests/data/Reader/XLSX/blankcell.xlsx';
        $reader = new Xlsx();
        $reader->setReadEmptyCells(false);
        $reloadedSpreadsheet = $reader->load($filename);
        self::assertFalse($reloadedSpreadsheet->getActiveSheet()->getCellCollection()->has('B2'));
        self::assertFalse($reloadedSpreadsheet->getActiveSheet()->getCellCollection()->has('C2'));
        self::assertTrue($reloadedSpreadsheet->getActiveSheet()->getCellCollection()->has('C3'));
        $reloadedSpreadsheet->disconnectWorksheets();
    }

    /**
     * Test generate file with some empty cells.
     *
     * @dataProvider providerSheetFormat
     */
    public function testLoadAndSaveReadEmpty(string $format, bool $expected): void
    {
        $filename = 'tests/data/Reader/XLSX/blankcell.xlsx';
        $reader = new Xlsx();
        //$reader->setReadEmptyCells(false);
        $spreadsheet = $reader->load($filename);
        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, $format);
        $spreadsheet->disconnectWorksheets();
        self::assertSame($expected, $reloadedSpreadsheet->getActiveSheet()->getCellCollection()->has('B2'));
        if ($expected) {
            self::assertContains($reloadedSpreadsheet->getActiveSheet()->getCell('B2')->getValue(), ['', null]);
        }
        self::assertFalse($reloadedSpreadsheet->getActiveSheet()->getCellCollection()->has('C2'));
        self::assertTrue($reloadedSpreadsheet->getActiveSheet()->getCellCollection()->has('C3'));
        $reloadedSpreadsheet->disconnectWorksheets();
    }

    /**
     * Test generate file with some empty cells.
     *
     * @dataProvider providerSheetFormat
     */
    public function testLoadAndSaveDontReadEmpty(string $format): void
    {
        $filename = 'tests/data/Reader/XLSX/blankcell.xlsx';
        $reader = new Xlsx();
        $reader->setReadEmptyCells(false);
        $spreadsheet = $reader->load($filename);
        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, $format);
        $spreadsheet->disconnectWorksheets();

        self::assertFalse($reloadedSpreadsheet->getActiveSheet()->getCellCollection()->has('B2'));
        self::assertFalse($reloadedSpreadsheet->getActiveSheet()->getCellCollection()->has('C2'));
        self::assertTrue($reloadedSpreadsheet->getActiveSheet()->getCellCollection()->has('C3'));
        $reloadedSpreadsheet->disconnectWorksheets();
    }
}
