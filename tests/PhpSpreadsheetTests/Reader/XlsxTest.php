<?php

namespace PhpOffice\PhpSpreadsheetTests\Reader;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx as ReaderXlsx;
use PhpOffice\PhpSpreadsheet\Shared\File;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as WriterXlsx;
use PHPUnit\Framework\TestCase;

class XlsxTest extends TestCase
{
    /**
     * Test load Xlsx file without cell reference.
     */
    public function testLoadXlsxWithoutCellReference()
    {
        $filename = './data/Reader/XLSX/without_cell_reference.xlsx';
        $reader = new ReaderXlsx();
        $reader->load($filename);
    }

    public function testFreezePane()
    {
        $filename = tempnam(File::sysGetTempDir(), 'phpspreadsheet');
        $colSplit = 1;
        $rowSplit = 1;
        $topLeftCell = 'B4';
        $spreadsheet = new Spreadsheet();
        $active = $spreadsheet->getActiveSheet();
        $active->freezePane($colSplit, $rowSplit, $topLeftCell);
        $writer = new WriterXlsx($spreadsheet);
        $writer->save($filename);
        // Read written file
        $reader = new ReaderXlsx();
        $reloadedSpreadsheet = $reader->load($filename);
        $reloadedActive = $reloadedSpreadsheet->getActiveSheet();
        $actualColSplit = $reloadedActive->getColSplit();
        $actualRowSplit = $reloadedActive->getRowSplit();
        $actualTopLeftCell = $reloadedActive->getTopLeftCell();
        self::assertSame($colSplit, $actualColSplit, 'should be able to set horizontal split');
        self::assertSame($rowSplit, $actualRowSplit, 'should be able to set vertical split');
        self::assertSame($topLeftCell, $actualTopLeftCell, 'should be able to set the top left cell');
    }
}
