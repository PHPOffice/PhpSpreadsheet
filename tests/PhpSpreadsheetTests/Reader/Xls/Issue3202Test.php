<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xls;

use PhpOffice\PhpSpreadsheet\Reader\Xls as XlsReader;
use PHPUnit\Framework\TestCase;

class Issue3202Test extends TestCase
{
    public function testSelectedCellWithConditionals(): void
    {
        // Unknown index notice when loading
        $filename = 'tests/data/Reader/XLS/issue.3202.xls';
        $reader = new XlsReader();
        $spreadsheet = $reader->load($filename);
        $sheet = $spreadsheet->getActiveSheet();
        self::assertSame('A2', $sheet->getSelectedCells());

        $collection = $sheet->getConditionalStylesCollection();
        self::assertCount(1, $collection);
        $conditionalArray = $collection['A1:A5'];
        self::assertCount(3, $conditionalArray);

        $conditions = $conditionalArray[0]->getConditions();
        self::assertCount(1, $conditions);
        self::assertSame('$A1=3', $conditions[0]);
        self::assertTrue($conditionalArray[0]->getNoFormatSet());
        self::assertTrue($conditionalArray[0]->getStopIfTrue());

        $conditions = $conditionalArray[1]->getConditions();
        self::assertCount(1, $conditions);
        self::assertSame('$A1>5', $conditions[0]);
        self::assertFalse($conditionalArray[1]->getNoFormatSet());
        self::assertTrue($conditionalArray[1]->getStopIfTrue());

        $conditions = $conditionalArray[2]->getConditions();
        self::assertCount(1, $conditions);
        self::assertSame('$A1>1', $conditions[0]);
        self::assertFalse($conditionalArray[2]->getNoFormatSet());
        self::assertTrue($conditionalArray[2]->getStopIfTrue());

        $spreadsheet->disconnectWorksheets();
    }
}
