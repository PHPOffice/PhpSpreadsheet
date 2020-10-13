<?php

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xml;

use PhpOffice\PhpSpreadsheet\Reader\Exception as ReaderException;
use PhpOffice\PhpSpreadsheet\Reader\Xml;
use PHPUnit\Framework\TestCase;

class XmlInfoTest extends TestCase
{
    public function testListNames(): void
    {
        $filename = __DIR__
            . '/../../../..'
            . '/samples/templates/excel2003.xml';
        $reader = new Xml();
        $names = $reader->listWorksheetNames($filename);
        self::assertCount(2, $names);
        self::assertEquals('Sample Data', $names[0]);
        self::assertEquals('Report Data', $names[1]);
    }

    public function testListNamesInvalidFile(): void
    {
        $this->expectException(ReaderException::class);
        $filename = __FILE__;
        $reader = new Xml();
        $names = $reader->listWorksheetNames($filename);
        self::assertNotEquals($names, $names);
    }

    public function testListInfo(): void
    {
        $filename = __DIR__
            . '/../../../..'
            . '/samples/templates/excel2003.xml';
        $reader = new Xml();
        $info = $reader->listWorksheetInfo($filename);
        $expected = [
            [
                'worksheetName' => 'Sample Data',
                'lastColumnLetter' => 'J',
                'lastColumnIndex' => 9,
                'totalRows' => 31,
                'totalColumns' => 10,
            ],
            [
                'worksheetName' => 'Report Data',
                'lastColumnLetter' => 'I',
                'lastColumnIndex' => 8,
                'totalRows' => 15,
                'totalColumns' => 9,
            ],
        ];
        self::assertEquals($expected, $info);
    }

    public function testListInfoInvalidFile(): void
    {
        $this->expectException(ReaderException::class);
        $filename = __FILE__;
        $reader = new Xml();
        $info = $reader->listWorksheetInfo($filename);
        self::assertNotEquals($info, $info);
    }

    public function testLoadInvalidFile(): void
    {
        $this->expectException(ReaderException::class);
        $filename = __FILE__;
        $reader = new Xml();
        $spreadsheet = $reader->load($filename);
        self::assertNotEquals($spreadsheet, $spreadsheet);
    }
}
