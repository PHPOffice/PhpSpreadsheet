<?php

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PHPUnit\Framework\TestCase;

class Issue2516Test extends TestCase
{
    /**
     * @var string
     */
    private static $testbook = 'tests/data/Reader/XLSX/issue.2516b.xlsx';

    public function testPreliminaries(): void
    {
        $file = 'zip://';
        $file .= self::$testbook;
        $file .= '#docProps/thumbnail.wmf';
        $data = file_get_contents($file);

        // confirm that file exists
        self::assertNotFalse($data, 'thumbnail.wmf not exists');

        $file = 'zip://';
        $file .= self::$testbook;
        $file .= '#_rels/.rels';
        $data = file_get_contents($file);
        // confirm that file contains expected namespaced xml tag
        if ($data === false) {
            self::fail('Unable to read file .rels');
        } else {
            self::assertStringContainsString('Type="http://schemas.openxmlformats.org/package/2006/relationships/metadata/thumbnail" Target="docProps/thumbnail.wmf"', $data);
        }
    }

    public function testIssue2516a(): void
    {
        $filename = self::$testbook;
        $reader = new Xlsx();
        $names = $reader->listWorksheetNames($filename);
        $expected = ['Sheet1'];
        self::assertSame($expected, $names);
    }

    public function testIssue2516b(): void
    {
        $filename = self::$testbook;
        $reader = new Xlsx();
        $infos = $reader->listWorksheetInfo($filename);
        $expected = [
            [
                'worksheetName' => 'Sheet1',
                'lastColumnLetter' => 'B',
                'lastColumnIndex' => 1,
                'totalRows' => '6',
                'totalColumns' => 2,
            ],
        ];
        self::assertSame($expected, $infos);
    }
}
