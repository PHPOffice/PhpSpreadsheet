<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx\Namespaces as XlsxNamespaces;
use PHPUnit\Framework\TestCase;
use SimpleXMLElement;

class Issue4505Test extends TestCase
{
    private static string $file = 'tests/data/Reader/XLSX/issue.4505.xlsx';

    public function testVmlProcessingWithXAndONamespaces(): void
    {
        $reader = new XlsxReader();
        $spreadsheet = $reader->load(self::$file);
        $sheet = $spreadsheet->getActiveSheet();

        $comments = $sheet->getComments();
        self::assertSame('Sheet1', $sheet->getTitle());

        if (!empty($comments)) {
            $comment = reset($comments);
            $comment->getText();
        }

        $spreadsheet->disconnectWorksheets();
    }

    public function testVmlFileContainsRequiredNamespaces(): void
    {
        $file = 'zip://' . self::$file . '#xl/drawings/vmlDrawing1.vml';
        $data = file_get_contents($file);

        if ($data === false) {
            self::markTestSkipped('Test file issue.4505.xlsx not found or VML file missing.');
        }

        self::assertStringContainsString('<v:shape', $data);
        self::assertStringContainsString('o:shapelayout', $data);
        self::assertStringContainsString('x:ClientData', $data);
        self::assertStringContainsString('o:insetmode', $data);
    }

    public function testXPathQueriesWithNamespaceRegistration(): void
    {
        $vmlContent = '<?xml version="1.0" encoding="UTF-8"?>
        <v:shape xmlns:v="urn:schemas-microsoft-com:vml"
                 xmlns:x="urn:schemas-microsoft-com:office:excel"
                 xmlns:o="urn:schemas-microsoft-com:office:office">
            <v:fill o:relid="rId1" o:title="Test Image"/>
            <x:ClientData ObjectType="Note">
                <x:Row>5</x:Row>
                <x:Column>2</x:Column>
                <x:TextHAlign>left</x:TextHAlign>
            </x:ClientData>
        </v:shape>';

        $shape = new SimpleXMLElement($vmlContent);

        $shape->registerXPathNamespace('v', XlsxNamespaces::URN_VML);
        $shape->registerXPathNamespace('x', XlsxNamespaces::URN_VML);
        $shape->registerXPathNamespace('o', XlsxNamespaces::URN_MSOFFICE);

        $clientData = $shape->xpath('.//x:ClientData');
        self::assertNotEmpty($clientData, 'XPath with x: prefix works with namespace registration');

        $relid = $shape->xpath('.//v:fill/@o:relid');
        self::assertNotEmpty($relid, 'XPath with o: prefix works with namespace registration');

        $row = $clientData[0]->xpath('.//x:Row');
        self::assertNotEmpty($row, 'Can access nested x: elements');
        self::assertEquals('5', (string) $row[0], 'Row value is correct');

        self::assertEquals('rId1', (string) $relid[0], 'RelId value is correct');
    }
}
