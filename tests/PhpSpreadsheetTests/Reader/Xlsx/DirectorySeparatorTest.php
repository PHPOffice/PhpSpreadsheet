<?php

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PHPUnit\Framework\TestCase;

class DirectorySeparatorTest extends TestCase
{
    /**
     * @dataProvider providerDirectorySeparator
     */
    public function testDirectorySeparatorIdentify(string $fileName): void
    {
        $filename = "tests/data/Reader/XLSX/{$fileName}";
        $reader = IOFactory::identify($filename);

        self::assertSame('Xlsx', $reader);
    }

    /**
     * @dataProvider providerDirectorySeparator
     */
    public function testDirectorySeparatorWorksheetNames(string $fileName): void
    {
        $filename = "tests/data/Reader/XLSX/{$fileName}";
        $reader = new Xlsx();
        $sheetList = $reader->listWorksheetNames($filename);

        self::assertCount(1, $sheetList);
        self::assertSame('Sheet', $sheetList[0]);
    }

    /**
     * @dataProvider providerDirectorySeparator
     */
    public function testDirectorySeparatorWorksheetInfo(string $fileName): void
    {
        $filename = "tests/data/Reader/XLSX/{$fileName}";
        $reader = new Xlsx();
        $sheetData = $reader->listWorksheetInfo($filename);

        self::assertCount(1, $sheetData);
        self::assertSame('Sheet', $sheetData[0]['worksheetName']);
        self::assertSame(3, (int) $sheetData[0]['totalRows']);
        self::assertSame(21, (int) $sheetData[0]['totalColumns']);
    }

    /**
     * @dataProvider providerDirectorySeparator
     */
    public function testDirectorySeparatorLoad(string $fileName): void
    {
        $filename = "tests/data/Reader/XLSX/{$fileName}";
        $reader = new Xlsx();
        $spreadsheet = $reader->load($filename);

        $cellValue = $spreadsheet->getActiveSheet()->getCell('A1')->getValue();

        self::assertSame('Key ID', $cellValue);
    }

    public static function providerDirectorySeparator(): array
    {
        return [
            ['Zip-Linux-Directory-Separator.xlsx'],
            ['Zip-Windows-Directory-Separator.xlsx'],
        ];
    }
}
