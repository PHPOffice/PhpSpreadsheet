<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Slk;

use PhpOffice\PhpSpreadsheet\Cell\StringValueBinder;
use PhpOffice\PhpSpreadsheet\Reader\Slk;
use PHPUnit\Framework\TestCase;

class BinderTest extends TestCase
{
    public function testBinder(): void
    {
        $infile = 'tests/data/Reader/Slk/issue.2276.slk';
        $reader = new Slk();
        $spreadsheet = $reader->load($infile);
        $sheet = $spreadsheet->getActiveSheet();
        $expected1 = [[1, 2], [3, '']];
        self::assertSame($expected1, $sheet->toArray(null, false, false));
        $reader2 = new Slk();
        $reader2->setValueBinder(new StringValueBinder());
        $spreadsheet2 = $reader2->load($infile);
        $sheet2 = $spreadsheet2->getActiveSheet();
        $expected2 = [['1', '2'], ['3', '']];
        self::assertSame($expected2, $sheet2->toArray(null, false, false));
        $spreadsheet->disconnectWorksheets();
        $spreadsheet2->disconnectWorksheets();
    }
}
