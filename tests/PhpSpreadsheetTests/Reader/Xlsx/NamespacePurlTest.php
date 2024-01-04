<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

class NamespacePurlTest extends \PHPUnit\Framework\TestCase
{
    private static string $testbook = 'tests/data/Reader/XLSX/namespacepurl.xlsx';

    public function testPreliminaries(): void
    {
        $file = 'zip://';
        $file .= self::$testbook;
        $file .= '#xl/workbook.xml';
        $data = file_get_contents($file);
        // confirm that file contains expected namespaced xml tag
        if ($data === false) {
            self::fail('Unable to read file');
        } else {
            self::assertStringContainsString('http://purl.oclc.org/ooxml/', $data);
        }
    }

    public function testPurlNamespace(): void
    {
        $filename = self::$testbook;
        $reader = new Xlsx();
        $actual = $reader->canRead($filename);
        self::assertTrue($actual);

        $sheets = $reader->listWorksheetNames($filename);
        self::assertEquals(['ml_out'], $sheets);

        $actual = $reader->listWorksheetInfo($filename);
        $expected = [
            [
                'worksheetName' => 'ml_out',
                'lastColumnLetter' => 'R',
                'lastColumnIndex' => 17,
                'totalRows' => '76',
                'totalColumns' => 18,
            ],
        ];

        self::assertEquals($expected, $actual);
    }

    public function testPurlLoad(): void
    {
        $filename = self::$testbook;
        $reader = new Xlsx();
        $spreadsheet = $reader->load($filename);
        $sheet = $spreadsheet->getActiveSheet();
        self::assertSame('ml_out', $sheet->getTitle());
        self::assertSame('Item', $sheet->getCell('A1')->getValue());
        self::assertEquals(97.91, $sheet->getCell('G3')->getValue());
    }
}
