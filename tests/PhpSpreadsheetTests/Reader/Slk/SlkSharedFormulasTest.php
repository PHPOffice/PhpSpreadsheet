<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Slk;

use PhpOffice\PhpSpreadsheet\Reader\Slk;

class SlkSharedFormulasTest extends \PHPUnit\Framework\TestCase
{
    public function testComments(): void
    {
        $testbook = 'tests/data/Reader/Slk/issue.2267c.slk';
        $reader = new Slk();
        $spreadsheet = $reader->load($testbook);
        $sheet = $spreadsheet->getActiveSheet();
        $range = 'A1:' . $sheet->getHighestDataColumn() . $sheet->getHighestDataRow();
        $values = $sheet->RangeToArray($range, null, false, false, false, false); // just get values, don't calculate
        $expected = [
            [1, 10, 100, 101, 102],
            ['=A1+1', '=B1+1', '=C1+1', '=D1+1', '=E1+1'],
            ['=A2+1', '=B2+1', '=C2+1', '=D2+1', '=E2+1'],
            ['=A3+1', '=B3+1', '=C3+1', '=D3+1', '=E3+1'],
            ['=A4+1', '=B4+1', '=C4+1', '=D4+1', '=E4+1'],
        ];
        self::assertSame($expected, $values);
        $calcValues = $sheet->RangeToArray($range, null, true, false, false, false); // get calculated values
        $expectedCalc = [
            [1, 10, 100, 101, 102],
            [2, 11, 101, 102, 103],
            [3, 12, 102, 103, 104],
            [4, 13, 103, 104, 105],
            [5, 14, 104, 105, 106],
        ];
        self::assertSame($expectedCalc, $calcValues);
        $spreadsheet->disconnectWorksheets();
    }
}
