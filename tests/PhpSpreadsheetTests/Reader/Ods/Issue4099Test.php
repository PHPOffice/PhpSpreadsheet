<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Ods;

use PhpOffice\PhpSpreadsheet\Reader\Ods as OdsReader;
use PHPUnit\Framework\TestCase;

class Issue4099Test extends TestCase
{
    private string $file = 'tests/data/Reader/Ods/issue.4099.ods';

    public function testNoHeaderFooterStyle(): void
    {
        // header-style and footer-style are missing in styles.xml
        $zipFile = 'zip://' . $this->file . '#styles.xml';
        $contents = (string) file_get_contents($zipFile);
        self::assertStringContainsString('page-layout ', $contents);
        self::assertStringNotContainsString('header-style', $contents);
        self::assertStringNotContainsString('footer-style', $contents);
        $reader = new OdsReader();
        $spreadsheet = $reader->load($this->file);
        $sheet = $spreadsheet->getActiveSheet();
        self::assertSame('FirstCell', $sheet->getCell('A1')->getValue());
        $spreadsheet->disconnectWorksheets();
    }
}
