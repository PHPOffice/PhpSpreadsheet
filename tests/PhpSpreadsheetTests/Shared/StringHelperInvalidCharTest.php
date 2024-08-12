<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Shared;

use PhpOffice\PhpSpreadsheet\Shared\StringHelper;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

class StringHelperInvalidCharTest extends TestCase
{
    public function testInvalidChar(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $substitution = '�';
        $array = [
            ['Normal string', 'Hello', 'Hello'],
            ['integer', 2, 2],
            ['float', 2.1, 2.1],
            ['boolean true', true, true],
            ['illegal FFFE/FFFF', "H\xef\xbf\xbe\xef\xbf\xbfello", "H{$substitution}{$substitution}ello"],
            ['illegal character', "H\xef\x00\x00ello", "H{$substitution}\x00\x00ello"],
            ['overlong character', "H\xc0\xa0ello", "H{$substitution}{$substitution}ello"],
            ['Osmanya as single character', "H\xf0\x90\x90\x80ello", 'H𐐀ello'],
            ['Osmanya as surrogate pair (x)', "\xed\xa0\x81\xed\xb0\x80", "{$substitution}{$substitution}{$substitution}{$substitution}{$substitution}{$substitution}"],
            ['Osmanya as surrogate pair (u)', "\u{d801}\u{dc00}", "{$substitution}{$substitution}{$substitution}{$substitution}{$substitution}{$substitution}"],
            ['Half surrogate pair (u)', "\u{d801}", "{$substitution}{$substitution}{$substitution}"],
            ['Control character', "\u{7}", "\u{7}"],
        ];

        $sheet->fromArray($array);
        $row = 0;
        foreach ($array as $value) {
            self::assertSame($value[1] === $value[2], StringHelper::isUTF8((string) $value[1]));
            ++$row;
            $expected = $value[2];
            self::assertIsString($sheet->getCell("A$row")->getValue());
            self::assertSame(
                $expected,
                $sheet->getCell("B$row")->getValue(),
                $sheet->getCell("A$row")->getValue()
            );
        }
    }
}
