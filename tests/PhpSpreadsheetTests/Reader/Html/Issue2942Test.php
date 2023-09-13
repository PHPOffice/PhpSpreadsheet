<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Html;

use PhpOffice\PhpSpreadsheet\Reader\Html;
use PHPUnit\Framework\TestCase;

class Issue2942Test extends TestCase
{
    public function testLoadFromString(): void
    {
        $content = '<table><tbody><tr><td>éàâèî</td></tr></tbody></table>';
        $reader = new Html();
        $spreadsheet = $reader->loadFromString($content);
        $sheet = $spreadsheet->getActiveSheet();
        self::assertSame('éàâèî', $sheet->getCell('A1')->getValue());
        $spreadsheet->disconnectWorksheets();
    }

    public function testLoadFromFile(): void
    {
        $file = 'tests/data/Reader/HTML/utf8chars.html';
        $reader = new Html();
        $spreadsheet = $reader->loadSpreadsheetFromFile($file);
        $sheet = $spreadsheet->getActiveSheet();
        self::assertSame('Test Utf-8 characters voilà', $sheet->getTitle());
        self::assertSame('éàâèî', $sheet->getCell('A1')->getValue());
        self::assertSame('αβγδε', $sheet->getCell('B1')->getValue());
        self::assertSame('𐐁𐐂𐐃 & だけち', $sheet->getCell('A2')->getValue());
        self::assertSame('אבגדה', $sheet->getCell('B2')->getValue());
        self::assertSame('𪔀𪔁𪔂', $sheet->getCell('C2')->getValue());
        self::assertSame('᠐᠑᠒', $sheet->getCell('A3')->getValue());
        self::assertSame('അആ', $sheet->getCell('B3')->getValue());
        self::assertSame('กขฃ', $sheet->getCell('C3')->getValue());
        self::assertSame('✀✐✠', $sheet->getCell('D3')->getValue());
        $spreadsheet->disconnectWorksheets();
    }

    public function testInfo(): void
    {
        $file = 'tests/data/Reader/HTML/utf8chars.charset.html';
        $reader = new Html();
        $info = $reader->listWorksheetInfo($file);
        self::assertCount(1, $info);
        $info0 = $info[0];
        self::assertSame('Test Utf-8 characters voilà', $info0['worksheetName']);
        self::assertSame('D', $info0['lastColumnLetter']);
        self::assertSame(3, $info0['lastColumnIndex']);
        self::assertSame(7, $info0['totalRows']);
        self::assertSame(4, $info0['totalColumns']);
        $names = $reader->listWorksheetNames($file);
        self::assertCount(1, $names);
        self::assertSame('Test Utf-8 characters voilà', $names[0]);

        // Following ignored, just make sure it's executable.
        $reader->setLoadSheetsOnly([$names[0]]);
        $spreadsheet = $reader->load($file);
        $sheet = $spreadsheet->getActiveSheet();
        self::assertSame('✀✐✠', $sheet->getCell('D3')->getValue());
        $spreadsheet->disconnectWorksheets();
    }
}
