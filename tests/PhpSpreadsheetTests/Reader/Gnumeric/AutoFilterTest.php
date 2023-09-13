<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Gnumeric;

use PhpOffice\PhpSpreadsheet\Reader\Gnumeric;
use PHPUnit\Framework\TestCase;

class AutoFilterTest extends TestCase
{
    public function testAutoFilterRange(): void
    {
        $filename = 'tests/data/Reader/Gnumeric/Autofilter_Basic.gnumeric';
        $reader = new Gnumeric();
        $spreadsheet = $reader->load($filename);
        $worksheet = $spreadsheet->getActiveSheet();

        $autoFilterRange = $worksheet->getAutoFilter()->getRange();

        self::assertSame('A1:D57', $autoFilterRange);
        $spreadsheet->disconnectWorksheets();
    }
}
