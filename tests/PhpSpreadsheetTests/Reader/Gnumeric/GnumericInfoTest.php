<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Gnumeric;

use PhpOffice\PhpSpreadsheet\Reader\Exception as ReaderException;
use PhpOffice\PhpSpreadsheet\Reader\Gnumeric;
use PHPUnit\Framework\TestCase;

class GnumericInfoTest extends TestCase
{
    public function testListNames(): void
    {
        $filename = 'samples/templates/GnumericTest.gnumeric';
        $reader = new Gnumeric();
        $names = $reader->listWorksheetNames($filename);
        self::assertCount(2, $names);
        self::assertEquals('Sample Data', $names[0]);
        self::assertEquals('Report Data', $names[1]);
    }

    public function testListInfo(): void
    {
        $filename = 'samples/templates/GnumericTest.gnumeric';
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

    public function testListNamesNotGumeric(): void
    {
        $this->expectException(ReaderException::class);
        $this->expectExceptionMessage('invalid Gnumeric file');
        $filename = 'samples/templates/excel2003.xml';
        $reader = new Gnumeric();
        $reader->listWorksheetNames($filename);
    }

    public function testListInfoNotXml(): void
    {
        $this->expectException(ReaderException::class);
        $this->expectExceptionMessage('invalid Gnumeric file');
        $filename = __FILE__;
        $reader = new Gnumeric();
        $reader->listWorksheetInfo($filename);
    }
}
