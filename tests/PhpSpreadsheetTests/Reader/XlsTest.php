<?php

namespace PhpOffice\PhpSpreadsheetTests\Reader;

use PhpOffice\PhpSpreadsheet\Reader\Xls as ReaderXls;
use PhpOffice\PhpSpreadsheet\Shared\File;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xls as WriterXls;
use PHPUnit_Framework_TestCase;

class XlsTest extends PHPUnit_Framework_TestCase
{
    public function testFreezePane()
    {
        $filename = tempnam(File::sysGetTempDir(), 'phpspreadsheet');

        $colSplit = 1;
        $rowSplit = 1;
        $topLeftCell = 'B4';

        $spreadsheet = new Spreadsheet();
        $active = $spreadsheet->getActiveSheet();
        $active->freezePane($colSplit, $rowSplit, $topLeftCell);

        $writer = new WriterXls($spreadsheet);
        $writer->save($filename);

        // Read written file
        $reader = new ReaderXls();
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
