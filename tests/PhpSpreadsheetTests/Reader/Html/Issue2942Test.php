<?php

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
    }
}
