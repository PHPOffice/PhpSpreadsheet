<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

class Issue3665Test extends \PHPUnit\Framework\TestCase
{
    private static string $testbook = 'tests/data/Reader/XLSX/issue.3665.xlsx';

    public function testPreliminaries(): void
    {
        $file = 'zip://';
        $file .= self::$testbook;
        $file .= '#xl/tables/table1.xml';
        $data = file_get_contents($file);
        // confirm that file contains expected namespaced xml tag
        if ($data === false) {
            self::fail('Unable to read table file');
        } else {
            self::assertStringContainsString('<x:table id="5" name="Table5" displayName="Table5" ref="A3:M4" tableType="xml" totalsRowShown="0" xmlns:x="http://schemas.openxmlformats.org/spreadsheetml/2006/main">', $data);
            self::assertStringContainsString('<x:autoFilter ref="A3:M4" />', $data);
        }

        $file = 'zip://';
        $file .= self::$testbook;
        $file .= '#xl/worksheets/_rels/sheet1.xml.rels';
        $data = file_get_contents($file);
        // confirm absolute path as reference
        if ($data === false) {
            self::fail('Unable to read rels file');
        } else {
            self::assertStringContainsString('Target="/xl/comments1.xml"', $data);
        }
    }

    public function testTable(): void
    {
        $reader = new Xlsx();
        $spreadsheet = $reader->load(self::$testbook);
        $sheet = $spreadsheet->getActiveSheet();
        $tables = $sheet->getTableCollection();
        self::assertCount(1, $tables);
        $table = $tables->offsetGet(0);
        if ($table === null) {
            self::fail('Unexpected failure obtaining table');
        } else {
            self::assertEquals('Table5', $table->getName());
            self::assertEquals('A3:M4', $table->getRange());
            self::assertTrue($table->getAllowFilter());
            self::assertSame('A3:M4', $table->getAutoFilter()->getRange());
        }
        $comment = $sheet->getComment('A3');
        self::assertSame('Code20', (string) $comment);
        $comment = $sheet->getComment('B3');
        self::assertSame('Text100', (string) $comment);
        $spreadsheet->disconnectWorksheets();
    }
}
