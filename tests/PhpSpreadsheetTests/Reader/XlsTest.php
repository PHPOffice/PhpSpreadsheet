<?php

namespace PhpOffice\PhpSpreadsheetTests\Reader;

use PhpOffice\PhpSpreadsheet\Reader\Xls as ReaderXls;
use PhpOffice\PhpSpreadsheet\Shared\File;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xls as WriterXls;
use PHPUnit_Framework_TestCase;

class XlsTest extends PHPUnit_Framework_TestCase
{
    public function testFreezePaneSplit()
    {
        $filename = tempnam(File::sysGetTempDir(), 'phpspreadsheet');

        $colSplit = 1;
        $rowSplit = 1;

        $spreadsheet = new Spreadsheet();
        $active = $spreadsheet->getActiveSheet();
        $active->createFreezePane($colSplit, $rowSplit);

        $leftMostColumn = $active->getLeftMostColumn();
        $topRow = $active->getTopRow();

        $writer = new WriterXls($spreadsheet);
        $writer->save($filename);

        // Read written file
        $reader = new ReaderXls();
        $reloadedSpreadsheet = $reader->load($filename);
        $reloadedActive = $reloadedSpreadsheet->getActiveSheet();
        $actualColSplit = $reloadedActive->getColSplit();
        $actualRowSplit = $reloadedActive->getRowSplit();
        $actualLeftMostColumn = $reloadedActive->getLeftMostColumn();
        $actualTopRow = $reloadedActive->getTopRow();

        self::assertSame($colSplit, $actualColSplit, 'should be able to set horizontal split');
        self::assertSame($rowSplit, $actualRowSplit, 'should be able to set vertical split');
        self::assertSame($leftMostColumn, $actualLeftMostColumn, 'should be able to set left most visible column');
        self::assertSame($topRow, $actualTopRow, 'should be able to set top most visible row');
    }

    public function testFreezePane()
    {
        $filename = tempnam(File::sysGetTempDir(), 'phpspreadsheet');

        $colSplit = 1;
        $rowSplit = 1;
        $leftMostColumn = 2;
        $topRow = 3;

        $spreadsheet = new Spreadsheet();
        $active = $spreadsheet->getActiveSheet();
        $active->createFreezePane($colSplit, $rowSplit, $leftMostColumn, $topRow);

        $writer = new WriterXls($spreadsheet);
        $writer->save($filename);

        // Read written file
        $reader = new ReaderXls();
        $reloadedSpreadsheet = $reader->load($filename);
        $reloadedActive = $reloadedSpreadsheet->getActiveSheet();
        $actualColSplit = $reloadedActive->getColSplit();
        $actualRowSplit = $reloadedActive->getRowSplit();
        $actualLeftMostColumn = $reloadedActive->getLeftMostColumn();
        $actualTopRow = $reloadedActive->getTopRow();

        self::assertSame($colSplit, $actualColSplit, 'should be able to set horizontal split');
        self::assertSame($rowSplit, $actualRowSplit, 'should be able to set vertical split');
        self::assertSame($leftMostColumn, $actualLeftMostColumn, 'should be able to set left most visible column');
        self::assertSame($topRow, $actualTopRow, 'should be able to set top most visible row');
    }
}
