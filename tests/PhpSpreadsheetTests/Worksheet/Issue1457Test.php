<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Worksheet;

use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PHPUnit\Framework\TestCase;

class Issue1457Test extends TestCase
{
    public function testIssue1457(): void
    {
        $sheet = new Worksheet();
        $sqref = 'C14:O15 C161:O1081 C16:H160 J16:O160 Q5';
        $sheet->protectCells($sqref);
        $protectedRanges = $sheet->getProtectedCellRanges();
        self::assertCount(1, $protectedRanges);
        $range0 = reset($protectedRanges);
        self::assertSame($sqref, $range0->getSqref());
        $expected = [
            ['C14', 'O15'],
            ['C161', 'O1081'],
            ['C16', 'H160'],
            ['J16', 'O160'],
            ['Q5'],
        ];
        self::assertSame($expected, $range0->allRanges());
    }
}
