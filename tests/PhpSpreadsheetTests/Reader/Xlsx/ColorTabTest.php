<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PHPUnit\Framework\TestCase;

class ColorTabTest extends TestCase
{
    public function testIssue2316(): void
    {
        $filename = 'tests/data/Reader/XLSX/colortabs.xlsx';
        $reader = new Xlsx();
        $spreadsheet = $reader->load($filename);

        // theme color
        self::assertSame('FF548135', $spreadsheet->getSheet(0)->getTabColor()->getArgb());
        // rgb color
        self::assertSame('FFFFC000', $spreadsheet->getSheet(1)->getTabColor()->getArgb());
        $spreadsheet->disconnectWorksheets();
    }
}
