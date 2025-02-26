<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xls;

use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PHPUnit\Framework\TestCase;

class Issue2463Test extends TestCase
{
    public function testNoUnknownIndexNotice(): void
    {
        // Unknown index notice when loading
        $filename = 'tests/data/Reader/XLS/issue.2463.xls';
        $reader = new Xls();
        $spreadsheet = $reader->load($filename);
        $sheet = $spreadsheet->getActiveSheet();
        self::assertSame('bol.com bestellingen', $sheet->getTitle());
        $spreadsheet->disconnectWorksheets();
    }
}
