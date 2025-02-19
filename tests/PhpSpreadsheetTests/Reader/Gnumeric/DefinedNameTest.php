<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Gnumeric;

use PhpOffice\PhpSpreadsheet\Reader\Gnumeric;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class DefinedNameTest extends TestCase
{
    public static function fileProvider(): array
    {
        return [
            ['tests/data/Reader/Gnumeric/apostrophe3a.gnumeric', 'Sheet1'],
            ['tests/data/Reader/Gnumeric/apostrophe3b.gnumeric', 'Apo\'strophe'],
        ];
    }

    #[DataProvider('fileProvider')]
    public function testDefinedName(string $filename, string $sheetName): void
    {
        $reader = new Gnumeric();
        $spreadsheet = $reader->load($filename);
        $sheet = $spreadsheet->getActiveSheet();
        self::assertSame($sheetName, $sheet->getTitle());
        self::assertSame('=sheet1first', $sheet->getCell('C1')->getValue());
        self::assertSame(1, $sheet->getCell('C1')->getCalculatedValue());
        $spreadsheet->disconnectWorksheets();
    }
}
