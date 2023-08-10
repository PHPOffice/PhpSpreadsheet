<?php

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xls;

use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PHPUnit\Framework\TestCase;

class InfoNamesTest extends TestCase
{
    public function testWorksheetNamesBiff5(): void
    {
        $filename = 'samples/templates/30templatebiff5.xls';
        $reader = new Xls();
        $names = $reader->listWorksheetNames($filename);
        $expected = ['Invoice', 'Terms and conditions'];
        self::assertSame($expected, $names);
    }

    public function testWorksheetInfoBiff5(): void
    {
        $filename = 'samples/templates/30templatebiff5.xls';
        $reader = new Xls();
        $info = $reader->listWorksheetInfo($filename);
        $expected = [
            [
                'worksheetName' => 'Invoice',
                'lastColumnLetter' => 'E',
                'lastColumnIndex' => 4,
                'totalRows' => 19,
                'totalColumns' => 5,
            ],
            [
                'worksheetName' => 'Terms and conditions',
                'lastColumnLetter' => 'B',
                'lastColumnIndex' => 1,
                'totalRows' => 3,
                'totalColumns' => 2,
            ],
        ];
        self::assertSame($expected, $info);
    }

    public function testWorksheetNamesBiff8(): void
    {
        $filename = 'samples/templates/31docproperties.xls';
        $reader = new Xls();
        $names = $reader->listWorksheetNames($filename);
        $expected = ['Worksheet'];
        self::assertSame($expected, $names);
    }

    public function testWorksheetInfoBiff8(): void
    {
        $filename = 'samples/templates/31docproperties.xls';
        $reader = new Xls();
        $info = $reader->listWorksheetInfo($filename);
        $expected = [
            [
                'worksheetName' => 'Worksheet',
                'lastColumnLetter' => 'B',
                'lastColumnIndex' => 1,
                'totalRows' => 1,
                'totalColumns' => 2,
            ],
        ];
        self::assertSame($expected, $info);
    }

    public function testWorksheetNamesBiff8Mac(): void
    {
        // Non-standard codepage
        $filename = 'tests/data/Reader/XLS/maccentraleurope.xls';
        $reader = new Xls();
        $names = $reader->listWorksheetNames($filename);
        $expected = ['Arkusz1'];
        self::assertSame($expected, $names);
    }

    public function testWorksheetInfoBiff8Mac(): void
    {
        $filename = 'tests/data/Reader/XLS/maccentraleurope.xls';
        $reader = new Xls();
        $info = $reader->listWorksheetInfo($filename);
        $expected = [
            [
                'worksheetName' => 'Arkusz1',
                'lastColumnLetter' => 'P',
                'lastColumnIndex' => 15,
                'totalRows' => 3,
                'totalColumns' => 16,
            ],
        ];
        self::assertSame($expected, $info);
    }
}
