<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Ods;

use PhpOffice\PhpSpreadsheet\Reader\Ods;
use PHPUnit\Framework\TestCase;

class Issue804Test extends TestCase
{
    public function testPreliminaries(): void
    {
        $file = 'zip://';
        $file .= 'tests/data/Reader/Ods/issue.804.ods';
        $file .= '#content.xml';
        $data = file_get_contents($file);
        // confirm that file contains expected namespaced xml tag
        if ($data === false) {
            self::fail('Unable to read file');
        } else {
            self::assertStringContainsString('<table:table-row>
          <table:table-cell office:value-type="string" table:number-rows-spanned="1" table:style-name="heading">
            <text:p>Name</text:p>', $data);
        }
    }

    public function testIssue2810(): void
    {
        // Whitespace between Xml nodes
        $filename = 'tests/data/Reader/Ods/issue.804.ods';
        $reader = new Ods();
        $spreadsheet = $reader->load($filename);
        $sheet = $spreadsheet->getActiveSheet();
        self::assertSame('Straße', $sheet->getCell('G1')->getValue());
        $spreadsheet->disconnectWorksheets();
    }
}
