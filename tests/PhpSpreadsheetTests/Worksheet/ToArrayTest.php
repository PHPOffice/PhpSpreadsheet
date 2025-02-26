<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Worksheet;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

class ToArrayTest extends TestCase
{
    public static function testToArray(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $inputArray = [
            ['a', 'b', null, 'd', 'e'],
            [],
            [null, null, null, null, 'f'],
            [],
            [1, 0, null, 4],
        ];

        $sheet->fromArray($inputArray, null, 'A1', true);

        $expected1 = [
            ['a', 'b', null, 'd', 'e'],
            [null, null, null, null, null],
            [null, null, null, null, 'f'],
            [null, null, null, null, null],
            [1, 0, null, 4, null],
        ];
        $result1 = $sheet->toArray(null, false, false, false, false);
        self::assertSame($expected1, $result1);

        $expected2 = [
            1 => ['A' => 'a', 'B' => 'b', 'C' => null, 'D' => 'd', 'E' => 'e'],
            2 => ['A' => null, 'B' => null, 'C' => null, 'D' => null, 'E' => null],
            3 => ['A' => null, 'B' => null, 'C' => null, 'D' => null, 'E' => 'f'],
            4 => ['A' => null, 'B' => null, 'C' => null, 'D' => null, 'E' => null],
            5 => ['A' => 1, 'B' => 0, 'C' => null, 'D' => 4, 'E' => null],
        ];
        $result2 = $sheet->toArray(null, false, false, true, false);
        self::assertSame($expected2, $result2);

        $expected3 = [
            [null, null, null, null],
            [null, null, null, 'f'],
            [null, null, null, null],
            [0, null, 4, null],
        ];
        $result3 = $sheet->rangeToArray('B2:E5', null, false, false, false, false);
        self::assertSame($expected3, $result3);

        $expected4 = [
            2 => ['B' => null, 'C' => null, 'D' => null, 'E' => null],
            3 => ['B' => null, 'C' => null, 'D' => null, 'E' => 'f'],
            4 => ['B' => null, 'C' => null, 'D' => null, 'E' => null],
            5 => ['B' => 0, 'C' => null, 'D' => 4, 'E' => null],
        ];
        $result4 = $sheet->rangeToArray('B2:E5', null, false, false, true, false);
        self::assertSame($expected4, $result4);

        $spreadsheet->disconnectWorksheets();
    }

    public static function testMaxCol(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getCell('A1')->setValue('start');
        $sheet->getCell('XFD1')->setValue('end');
        $array = $sheet->toArray(null, false, false, false, false);
        self::assertCount(1, $array);
        self::assertCount(16384, $array[0]);
        self::assertSame('start', $array[0][0]);
        self::assertSame('end', $array[0][16383]);
    }
}
