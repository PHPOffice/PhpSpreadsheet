<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xml;

use PhpOffice\PhpSpreadsheet\Reader\Exception as ReaderException;
use PhpOffice\PhpSpreadsheet\Reader\Xml;
use PHPUnit\Framework\TestCase;

class XmlInfoTest extends TestCase
{
    public function testListNames(): void
    {
        $filename = 'samples/templates/excel2003.xml';
        $reader = new Xml();
        $names = $reader->listWorksheetNames($filename);
        self::assertCount(2, $names);
        self::assertEquals('Sample Data', $names[0]);
        self::assertEquals('Report Data', $names[1]);
    }

    public function testListNamesInvalidFile(): void
    {
        $this->expectException(ReaderException::class);
        $this->expectExceptionMessage('Invalid Spreadsheet file');
        $filename = __FILE__;
        $reader = new Xml();
        $names = $reader->listWorksheetNames($filename);
        self::assertNotEquals($names, $names);
    }

    public function testListNamesGnumericFile(): void
    {
        $this->expectException(ReaderException::class);
        $this->expectExceptionMessage('Invalid Spreadsheet file');
        $filename = 'tests/data/Reader/Gnumeric/PageSetup.gnumeric.unzipped.xml';
        $reader = new Xml();
        $names = $reader->listWorksheetNames($filename);
        self::assertNotEquals($names, $names);
    }

    public function testListInfo(): void
    {
        $filename = 'samples/templates/excel2003.xml';
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
        $this->expectExceptionMessage('Invalid Spreadsheet file');
        $filename = __FILE__;
        $reader = new Xml();
        $info = $reader->listWorksheetInfo($filename);
        self::assertNotEquals($info, $info);
    }

    public function testListInfoGnumericFile(): void
    {
        $this->expectException(ReaderException::class);
        $this->expectExceptionMessage('Invalid Spreadsheet file');
        $filename = 'tests/data/Reader/Gnumeric/PageSetup.gnumeric.unzipped.xml';
        $reader = new Xml();
        $info = $reader->listWorksheetInfo($filename);
        self::assertNotEquals($info, $info);
    }

    public function testLoadInvalidFile(): void
    {
        $this->expectException(ReaderException::class);
        $this->expectExceptionMessage('Invalid Spreadsheet file');
        $filename = __FILE__;
        $reader = new Xml();
        $spreadsheet = $reader->load($filename);
        self::assertNotEquals($spreadsheet, $spreadsheet);
    }

    public function testLoadGnumericFile(): void
    {
        $this->expectException(ReaderException::class);
        $this->expectExceptionMessage('Invalid Spreadsheet file');
        $filename = 'tests/data/Reader/Gnumeric/PageSetup.gnumeric.unzipped.xml';
        $reader = new Xml();
        $spreadsheet = $reader->load($filename);
        self::assertNotEquals($spreadsheet, $spreadsheet);
    }
}
