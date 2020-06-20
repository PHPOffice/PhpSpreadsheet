<?php

namespace PhpOffice\PhpSpreadsheetTests\Reader\Gnumeric;

use PhpOffice\PhpSpreadsheet\Reader\Gnumeric;
use PHPUnit\Framework\TestCase;

class GnumericInfoTest extends TestCase
{
    public function testListNames(): void
    {
        $filename = __DIR__
            . '/../../../..'
            . '/samples/templates/GnumericTest.gnumeric';
        $reader = new Gnumeric();
        $names = $reader->listWorksheetNames($filename);
        self::assertCount(2, $names);
        self::assertEquals('Sample Data', $names[0]);
        self::assertEquals('Report Data', $names[1]);
    }

    public function testListInfo(): void
    {
        $filename = __DIR__
            . '/../../../..'
            . '/samples/templates/GnumericTest.gnumeric';
        $reader = new Gnumeric();
        $info = $reader->listWorksheetInfo($filename);
        $expected = [
            [
                'worksheetName' => 'Sample Data',
                'lastColumnLetter' => 'N',
                'lastColumnIndex' => 13,
                'totalRows' => 31,
                'totalColumns' => 14,
            ],
            [
                'worksheetName' => 'Report Data',
                'lastColumnLetter' => 'K',
                'lastColumnIndex' => 10,
                'totalRows' => 65535,
                'totalColumns' => 11,
            ],
        ];
        self::assertEquals($expected, $info);
    }
}
