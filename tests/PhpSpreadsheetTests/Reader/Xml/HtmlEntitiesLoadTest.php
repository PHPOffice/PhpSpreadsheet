<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xml;

use PhpOffice\PhpSpreadsheet\Reader\Xml as XmlReader;
use PHPUnit\Framework\TestCase;

class HtmlEntitiesLoadTest extends TestCase
{
    public static function testIssue2157(): void
    {
        $infile = 'tests/data/Reader/Xml/issue.2157.small.xml';
        $contents = (string) file_get_contents($infile);
        self::assertSame("\t", substr($contents, 0, 1));
        self::assertStringContainsString('&Tau;', $contents);
        self::assertStringContainsString('&lt;/br&gt', $contents);
        self::assertStringNotContainsString('Τ', $contents); // that's a Tau, not T
        self::assertStringNotContainsString('</br>', $contents);
        $reader = new XmlReader();
        $spreadsheet = $reader->load($infile);
        $sheet = $spreadsheet->getActiveSheet();
        self::assertSame('Τέλεια όραση χωρίς γυαλιά', $sheet->getCell('E2')->getValue());
        $g2 = $sheet->getCell('G2')->getValue();
        self::assertStringContainsString('</br>', $g2);
        $spreadsheet->disconnectWorksheets();
    }
}
