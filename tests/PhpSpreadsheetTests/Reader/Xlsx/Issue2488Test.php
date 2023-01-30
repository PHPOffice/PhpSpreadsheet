<?php

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PHPUnit\Framework\TestCase;

class Issue2488Test extends TestCase
{
    /**
     * @var string
     */
    private static $testbook = 'tests/data/Reader/XLSX/issue.2488.xlsx';

    public function testPreliminaries(): void
    {
        $file = 'zip://';
        $file .= self::$testbook;
        $file .= '#xl/worksheets/sheet1.xml';
        $data = file_get_contents($file);
        // confirm that file contains expected namespaced xml tag
        if ($data === false) {
            self::fail('Unable to read file');
        } else {
            self::assertStringContainsString('<c r="E1" t="n" />', $data);
            self::assertStringContainsString('<c r="E2" t="s" />', $data);
            self::assertStringContainsString('<c r="D3" t="b" />', $data);
        }
    }

    public function testIssue2488(): void
    {
        // Cell explicitly typed as numeric but without value.
        $filename = self::$testbook;
        $reader = IOFactory::createReader('Xlsx');
        $spreadsheet = $reader->load($filename);
        $sheet = $spreadsheet->getActiveSheet();
        // E1 and D3 are numeric/boolean without value.
        // So is E2, but I don't see a practical difference
        //    between null string and null in that case.
        $expected = [
            ['1', '2', '3', '0', null, '-1', '-2', '-3'],
            ['a', 'b', 'c', 'xxx', '', 'd', 'e', 'f'],
            ['FALSE', 'FALSE', 'FALSE', null, 'TRUE', 'TRUE', 'TRUE', 'TRUE'],
        ];
        self::assertSame($expected, $sheet->toArray());
        $expected = [
            ['1', '2', '3', '0', '', '-1', '-2', '-3'],
            ['a', 'b', 'c', 'xxx', '', 'd', 'e', 'f'],
            ['FALSE', 'FALSE', 'FALSE', '', 'TRUE', 'TRUE', 'TRUE', 'TRUE'],
        ];
        self::assertSame($expected, $sheet->toArray(''));
        $expected = [
            [1, 2, 3, 0, null, -1, -2, -3],
            ['a', 'b', 'c', 'xxx', '', 'd', 'e', 'f'],
            [false, false, false, null, true, true, true, true],
        ];
        self::assertSame($expected, $sheet->toArray(null, true, false));

        $spreadsheet->disconnectWorksheets();
    }
}
