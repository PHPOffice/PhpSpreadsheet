<?php

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xls;

use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PHPUnit\Framework\TestCase;

class HiddenMergeCellsTest extends TestCase
{
    private const FILENAME = 'tests/data/Reader/XLS/HiddenMergeCellsTest.xls';

    public function testHiddenMergeCells(): void
    {
        $reader = new Xls();
        $spreadsheet = $reader->load(self::FILENAME);
        $c2InMergeRange = $spreadsheet->getActiveSheet()->getCell('C2')->isInMergeRange();
        self::assertTrue($c2InMergeRange);
        $a2InMergeRange = $spreadsheet->getActiveSheet()->getCell('A2')->isInMergeRange();
        self::assertTrue($a2InMergeRange);
        $a2MergeRangeValue = $spreadsheet->getActiveSheet()->getCell('A2')->isMergeRangeValueCell();
        self::assertTrue($a2MergeRangeValue);

        $cellArray = $spreadsheet->getActiveSheet()->rangeToArray('A2:C2');
        self::assertSame([['12', '4', '3']], $cellArray);
        $cellArray = $spreadsheet->getActiveSheet()->rangeToArray('A2:C2', null, true, false);
        self::assertSame([[12, 4, 3]], $cellArray);
        $spreadsheet->disconnectWorksheets();
    }

    public function testUnmergeHiddenMergeCells(): void
    {
        $reader = new Xls();
        $spreadsheet = $reader->load(self::FILENAME);
        $spreadsheet->getActiveSheet()->unmergeCells('A2:C2');

        $c2InMergeRange = $spreadsheet->getActiveSheet()->getCell('C2')->isInMergeRange();
        self::assertFalse($c2InMergeRange);
        $a2InMergeRange = $spreadsheet->getActiveSheet()->getCell('A2')->isInMergeRange();
        self::assertFalse($a2InMergeRange);

        $cellArray = $spreadsheet->getActiveSheet()->rangeToArray('A2:C2', null, false, false, false);
        self::assertSame([[12, '=6-B1', '=A2/B2']], $cellArray);
        $spreadsheet->disconnectWorksheets();
    }
}
