<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

class Issue4039Test extends \PHPUnit\Framework\TestCase
{
    private static string $testbook = 'tests/data/Style/ConditionalFormatting/CellMatcher.xlsx';

    public function testSplitRange(): void
    {
        $reader = new Xlsx();
        $spreadsheet = $reader->load(self::$testbook);
        $sheet = $spreadsheet->getSheetByNameOrThrow('cellIs Expression');
        $expected = [
            'A12:D17 A20', // split range
            'A22:D27',
            'A2:E6',
        ];
        self::assertSame($expected, array_keys($sheet->getConditionalStylesCollection()));
        self::assertSame($expected[0], $sheet->getConditionalRange('A20'));
        self::assertSame($expected[0], $sheet->getConditionalRange('C15'));
        self::assertNull($sheet->getConditionalRange('A19'));
        self::assertSame($expected[1], $sheet->getConditionalRange('D25'));
        $spreadsheet->disconnectWorksheets();
    }
}
