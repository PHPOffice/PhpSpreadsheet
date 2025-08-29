<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader;

use PhpOffice\PhpSpreadsheet\Exception as SpreadsheetException;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class CreateBlankSheetIfNoneReadTest extends TestCase
{
    #[DataProvider('providerIdentify')]
    public function testExceptionIfNoSheet(string $file, string $expectedName, string $expectedClass): void
    {
        $this->expectException(SpreadsheetException::class);
        $this->expectExceptionMessage('out of bounds index: 0');
        $actual = IOFactory::identify($file);
        self::assertSame($expectedName, $actual);
        $reader = IOFactory::createReaderForFile($file);
        self::assertSame($expectedClass, $reader::class);
        $sheetlist = ['Unknown sheetname'];
        $reader->setLoadSheetsOnly($sheetlist);
        $reader->load($file);
    }

    #[DataProvider('providerIdentify')]
    public function testCreateSheetIfNoSheet(string $file, string $expectedName, string $expectedClass): void
    {
        $actual = IOFactory::identify($file);
        self::assertSame($expectedName, $actual);
        $reader = IOFactory::createReaderForFile($file);
        self::assertSame($expectedClass, $reader::class);
        $reader->setCreateBlankSheetIfNoneRead(true);
        $sheetlist = ['Unknown sheetname'];
        $reader->setLoadSheetsOnly($sheetlist);
        $spreadsheet = $reader->load($file);
        $sheet = $spreadsheet->getActiveSheet();
        self::assertSame('Worksheet', $sheet->getTitle());
        self::assertCount(1, $spreadsheet->getAllSheets());
        $spreadsheet->disconnectWorksheets();
    }

    public static function providerIdentify(): array
    {
        return [
            ['samples/templates/26template.xlsx', 'Xlsx', Reader\Xlsx::class],
            ['samples/templates/GnumericTest.gnumeric', 'Gnumeric', Reader\Gnumeric::class],
            ['samples/templates/30template.xls', 'Xls', Reader\Xls::class],
            ['samples/templates/OOCalcTest.ods', 'Ods', Reader\Ods::class],
            ['samples/templates/excel2003.xml', 'Xml', Reader\Xml::class],
        ];
    }

    public function testUsingFlage(): void
    {
        $file = 'samples/templates/26template.xlsx';
        $reader = IOFactory::createReaderForFile($file);
        $sheetlist = ['Unknown sheetname'];
        $reader->setLoadSheetsOnly($sheetlist);
        $spreadsheet = $reader->load($file, Reader\BaseReader::CREATE_BLANK_SHEET_IF_NONE_READ);
        $sheet = $spreadsheet->getActiveSheet();
        self::assertSame('Worksheet', $sheet->getTitle());
        self::assertCount(1, $spreadsheet->getAllSheets());
        $spreadsheet->disconnectWorksheets();
    }
}
