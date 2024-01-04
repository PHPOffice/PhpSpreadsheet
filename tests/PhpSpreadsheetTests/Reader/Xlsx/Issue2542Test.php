<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PHPUnit\Framework\TestCase;

class Issue2542Test extends TestCase
{
    private static string $testbook = 'tests/data/Reader/XLSX/issue.2542.xlsx';

    public function testPreliminaries(): void
    {
        // Rich text without 'sz' tag
        $file = 'zip://';
        $file .= self::$testbook;
        $file .= '#xl/sharedStrings.xml';
        $data = file_get_contents($file);

        // confirm that file contains expected namespaced xml tag
        if ($data === false) {
            self::fail('Unable to read file sharedStrings.xml');
        } else {
            self::assertStringContainsString('<si><r><rPr><rFont val="Arial"/><b/><color theme="1"/></rPr><t xml:space="preserve">Factor group
</t></r><r><rPr><rFont val="Arial"/><b val="0"/><color theme="1"/></rPr><t>(for Rental items only)</t></r></si>', $data);
        }
    }

    public function testIssue2542(): void
    {
        $filename = self::$testbook;
        $reader = new Xlsx();
        $spreadsheet = $reader->load($filename);
        $sheet = $spreadsheet->getActiveSheet();
        $value = $sheet->getCell('P1')->getValue();
        if ($value instanceof RichText) {
            self::assertSame("Factor group\n(for Rental items only)", $value->getPlainText());
        } else {
            self::fail('Cell P1 is not RichText');
        }
        $spreadsheet->disconnectWorksheets();
    }
}
