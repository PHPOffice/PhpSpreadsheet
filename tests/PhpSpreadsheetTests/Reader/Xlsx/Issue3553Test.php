<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PHPUnit\Framework\TestCase;

class Issue3553Test extends TestCase
{
    private const DIR1 = 'tests/data/Reader/XLSX';

    private const FILENAME = 'issue.3553.xlsx';

    private static string $testbook = self::DIR1 . '/' . self::FILENAME;

    private const DIR2 = self::DIR1 . '/5042temp';

    public function testPreliminaries(): void
    {
        $file = 'zip://';
        $file .= self::$testbook;
        $file .= '#_rels/.rels';
        $data = file_get_contents($file);
        // confirm that file contains expected namespaced xml tag
        if ($data === false) {
            self::fail('Unable to read file');
        } else {
            self::assertStringContainsString('<Relationship Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="/xl/workbook.xml" Id="rId2" />', $data, 'Unexpected leading slash in Target attribute');
        }
    }

    public function testIssue3553(): void
    {
        $reader = new Xlsx();
        $expected = [[
            'worksheetName' => 'LinkTest',
            'lastColumnLetter' => 'B',
            'lastColumnIndex' => 1,
            'totalRows' => '3',
            'totalColumns' => 2,
        ]];
        self::assertSame($expected, $reader->listWorksheetInfo(self::$testbook));
        self::assertSame(['LinkTest'], $reader->listWorksheetNames(self::$testbook));
        $spreadsheet = $reader->load(self::$testbook);
        $sheet = $spreadsheet->getActiveSheet();
        self::assertSame('https://microsoft.com/', $sheet->getCell('B2')->getHyperlink()->getUrl());

        $spreadsheet->disconnectWorksheets();
    }

    // Must run in separate process because of chdir
    /** @runInSeparateProcess */
    public static function testUnexpectedDirectory(): void
    {
        $reader = new Xlsx();
        self::assertTrue(chdir(self::DIR2));
        $expected = [[
            'worksheetName' => 'LinkTest',
            'lastColumnLetter' => 'B',
            'lastColumnIndex' => 1,
            'totalRows' => '3',
            'totalColumns' => 2,
        ]];
        self::assertSame($expected, $reader->listWorksheetInfo(self::FILENAME));
        self::assertSame(['LinkTest'], $reader->listWorksheetNames(self::FILENAME));
        $spreadsheet = $reader->load(self::FILENAME);
        $sheet = $spreadsheet->getActiveSheet();
        self::assertSame('https://microsoft.com/', $sheet->getCell('B2')->getHyperlink()->getUrl());

        $spreadsheet->disconnectWorksheets();
    }
}
