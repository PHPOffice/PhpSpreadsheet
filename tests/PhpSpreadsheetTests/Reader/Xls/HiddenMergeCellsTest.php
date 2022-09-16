<?php

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xls;

use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

class HiddenMergeCellsTest extends TestCase
{
    /**
     * @var Spreadsheet
     */
    private $spreadsheet;

    protected function setup(): void
    {
        $filename = 'tests/data/Reader/XLS/HiddenMergeCellsTest.xls';
        $reader = new Xls();
        $this->spreadsheet = $reader->load($filename);
    }

    public function testHiddenMergeCells(): void
    {
        $c2InMergeRange = $this->spreadsheet->getActiveSheet()->getCell('C2')->isInMergeRange();
        self::assertTrue($c2InMergeRange);
        $a2InMergeRange = $this->spreadsheet->getActiveSheet()->getCell('A2')->isInMergeRange();
        self::assertTrue($a2InMergeRange);
        $a2MergeRangeValue = $this->spreadsheet->getActiveSheet()->getCell('A2')->isMergeRangeValueCell();
        self::assertTrue($a2MergeRangeValue);

        $cellArray = $this->spreadsheet->getActiveSheet()->rangeToArray('A2:C2');
        self::assertSame([[12, 4, 3]], $cellArray);
    }

    public function testUnmergeHiddenMergeCells(): void
    {
        $this->spreadsheet->getActiveSheet()->unmergeCells('A2:C2');

        $c2InMergeRange = $this->spreadsheet->getActiveSheet()->getCell('C2')->isInMergeRange();
        self::assertFalse($c2InMergeRange);
        $a2InMergeRange = $this->spreadsheet->getActiveSheet()->getCell('A2')->isInMergeRange();
        self::assertFalse($a2InMergeRange);

        $cellArray = $this->spreadsheet->getActiveSheet()->rangeToArray('A2:C2', null, false, false, false);
        self::assertSame([[12, '=6-B1', '=A2/B2']], $cellArray);
    }
}
