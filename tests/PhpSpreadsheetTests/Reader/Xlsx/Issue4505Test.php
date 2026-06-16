<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PHPUnit\Framework\TestCase;

class Issue4505Test extends TestCase
{
    private static string $file = 'tests/data/Reader/XLSX/issue.4505.namespace.xlsx';

    public function testVmlProcessingWithXAndONamespaces(): void
    {
        $reader = new XlsxReader();
        $spreadsheet = $reader->load(self::$file);
        $sheet = $spreadsheet->getActiveSheet();

        $comments = $sheet->getComments();
        self::assertArrayHasKey('A1', $comments);
        self::assertSame('right', $comments['A1']->getAlignment());
        self::assertSame("Some User:\nHello", (string) $comments['A1']->getText());
        $spreadsheet->disconnectWorksheets();
    }

    public function testVmlFileContainsRequiredNamespaces(): void
    {
        $file = 'zip://' . self::$file . '#xl/drawings/vmlDrawing1.vml';
        $data = (string) file_get_contents($file);

        self::assertStringContainsString('<ns1:shape ', $data); // usually v:shape
        self::assertStringContainsString('<ns3:shapelayout ns1:ext="edit">', $data); // usually o:shapelayout v:ext
        self::assertStringContainsString('<ns2:ClientData ObjectType="Note">', $data); // usually x:ClientData
        self::assertStringContainsString('ns3:insetmode', $data); // usually o:insetmode
        self::assertStringContainsString(
            '<ns2:TextHAlign>Right</ns2:TextHAlign>', // usually x:TextHAlign
            $data
        );
    }
}
