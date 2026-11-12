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
        $reader->listWorksheetNames($filename);
    }

    public function testListNamesGnumericFile(): void
    {
        $this->expectException(ReaderException::class);
        $this->expectExceptionMessage('Invalid Spreadsheet file');
        $filename = 'tests/data/Reader/Gnumeric/PageSetup.gnumeric.unzipped.xml';
        $reader = new Xml();
        $reader->listWorksheetNames($filename);
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
                'sheetState' => 'visible',
            ],
            [
                'worksheetName' => 'Report Data',
                'lastColumnLetter' => 'I',
                'lastColumnIndex' => 8,
                'totalRows' => 15,
                'totalColumns' => 9,
                'sheetState' => 'visible',
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
        $reader->listWorksheetInfo($filename);
    }

    public function testListInfoGnumericFile(): void
    {
        $this->expectException(ReaderException::class);
        $this->expectExceptionMessage('Invalid Spreadsheet file');
        $filename = 'tests/data/Reader/Gnumeric/PageSetup.gnumeric.unzipped.xml';
        $reader = new Xml();
        $reader->listWorksheetInfo($filename);
    }

    public function testLoadInvalidFile(): void
    {
        $this->expectException(ReaderException::class);
        $this->expectExceptionMessage('Invalid Spreadsheet file');
        $filename = __FILE__;
        $reader = new Xml();
        $reader->load($filename);
    }

    public function testLoadGnumericFile(): void
    {
        $this->expectException(ReaderException::class);
        $this->expectExceptionMessage('Invalid Spreadsheet file');
        $filename = 'tests/data/Reader/Gnumeric/PageSetup.gnumeric.unzipped.xml';
        $reader = new Xml();
        $reader->load($filename);
    }
}
