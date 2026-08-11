<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Ods;

use PhpOffice\PhpSpreadsheet\Reader\Ods as OdsReader;
use PHPUnit\Framework\TestCase;

class Issue4435Test extends TestCase
{
    private string $file = 'tests/data/Reader/Ods/issue.4435b.ods';

    public function testNoHeaderFooterStyle(): void
    {
        // had been throwing exception when cell didn't have value-type
        $zipFile = 'zip://' . $this->file . '#content.xml';
        $contents = (string) file_get_contents($zipFile);
        self::assertStringContainsString(
            '<table:table-cell table:style-name="ce1">' . "\n"
            . '<text:p/>' . "\n"
            . '</table:table-cell>',
            $contents
        );
        $reader = new OdsReader();
        $spreadsheet = $reader->load($this->file);
        $sheet = $spreadsheet->getActiveSheet();
        self::assertNull($sheet->getCell('B1')->getValue());
        $spreadsheet->disconnectWorksheets();
    }
}
