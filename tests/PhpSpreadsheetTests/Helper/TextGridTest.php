<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Helper;

use PhpOffice\PhpSpreadsheet\Helper\TextGrid;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class TextGridTest extends TestCase
{
    /** @param mixed[] $expected */
    #[DataProvider('providerTextGrid')]
    public function testTextGrid(
        bool $cli,
        bool $rowDividers,
        bool $rowHeaders,
        bool $columnHeaders,
        array $expected
    ): void {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray([
            [6, '=TEXT(A1,"yyyy-mm-dd hh:mm")', null, 0.572917],
            ['="6"', '=TEXT(A2,"yyyy-mm-dd hh:mm")', null, '1<>2'],
            ['xyz', '=TEXT(A3,"yyyy-mm-dd hh:mm")'],
        ], strictNullComparison: true);
        /** @var mixed[][] */
        $temp = $sheet->toArray(null, true, true, true);
        $textGrid = new TextGrid(
            $temp,
            $cli,
            rowDividers: $rowDividers,
            rowHeaders: $rowHeaders,
            columnHeaders: $columnHeaders
        );
        $result = $textGrid->render();
        // Note that, for cli, string will end with PHP_EOL,
        //    so explode will add an extra null-string element
        //    to its array output.
        $lines = explode(PHP_EOL, $result);
        self::assertSame($expected, $lines);
        $spreadsheet->disconnectWorksheets();
    }

    public static function providerTextGrid(): array
    {
        return [
            'cli default values' => [
                true, false, true, true,
                [
                    '    +-----+------------------+---+----------+',
                    '    | A   | B                | C | D        |',
                    '+---+-----+------------------+---+----------+',
                    '| 1 | 6   | 1900-01-06 00:00 |   | 0.572917 |',
                    '| 2 | 6   | 1900-01-06 00:00 |   | 1<>2     |',
                    '| 3 | xyz | xyz              |   |          |',
                    '+---+-----+------------------+---+----------+',
                    '',
                ],
            ],
            'html default values' => [
                false, false, true, true,
                [
                    '<pre>',
                    '    +-----+------------------+---+----------+',
                    '    | A   | B                | C | D        |',
                    '+---+-----+------------------+---+----------+',
                    '| 1 | 6   | 1900-01-06 00:00 |   | 0.572917 |',
                    '| 2 | 6   | 1900-01-06 00:00 |   | 1&lt;&gt;2     |',
                    '| 3 | xyz | xyz              |   |          |',
                    '+---+-----+------------------+---+----------+',
                    '</pre>',
                ],
            ],
            'cli rowDividers' => [
                true, true, true, true,
                [
                    '    +-----+------------------+---+----------+',
                    '    | A   | B                | C | D        |',
                    '+---+-----+------------------+---+----------+',
                    '| 1 | 6   | 1900-01-06 00:00 |   | 0.572917 |',
                    '+---+-----+------------------+---+----------+',
                    '| 2 | 6   | 1900-01-06 00:00 |   | 1<>2     |',
                    '+---+-----+------------------+---+----------+',
                    '| 3 | xyz | xyz              |   |          |',
                    '+---+-----+------------------+---+----------+',
                    '',
                ],
            ],
            'cli no columnHeaders' => [
                true, false, true, false,
                [
                    '+---+-----+------------------+--+----------+',
                    '| 1 | 6   | 1900-01-06 00:00 |  | 0.572917 |',
                    '| 2 | 6   | 1900-01-06 00:00 |  | 1<>2     |',
                    '| 3 | xyz | xyz              |  |          |',
                    '+---+-----+------------------+--+----------+',
                    '',
                ],
            ],
            'cli no row headers' => [
                true, false, false, true,
                [
                    '+-----+------------------+---+----------+',
                    '| A   | B                | C | D        |',
                    '+-----+------------------+---+----------+',
                    '| 6   | 1900-01-06 00:00 |   | 0.572917 |',
                    '| 6   | 1900-01-06 00:00 |   | 1<>2     |',
                    '| xyz | xyz              |   |          |',
                    '+-----+------------------+---+----------+',
                    '',
                ],
            ],
            'cli row dividers, no row nor column headers' => [
                true, true, false, false,
                [
                    '+-----+------------------+--+----------+',
                    '| 6   | 1900-01-06 00:00 |  | 0.572917 |',
                    '+-----+------------------+--+----------+',
                    '| 6   | 1900-01-06 00:00 |  | 1<>2     |',
                    '+-----+------------------+--+----------+',
                    '| xyz | xyz              |  |          |',
                    '+-----+------------------+--+----------+',
                    '',
                ],
            ],
        ];
    }

    public function testBool(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray([
            [0, 1],
            [true, false],
            [true, true],
        ], strictNullComparison: true);
        /** @var mixed[][] */
        $temp = $sheet->toArray(null, true, false, true);
        $textGrid = new TextGrid(
            $temp,
            true,
            rowDividers: false,
            rowHeaders: false,
            columnHeaders: false,
        );
        $expected = [
            '+------+-------+',
            '| 0    | 1     |',
            '| TRUE | FALSE |',
            '| TRUE | TRUE  |',
            '+------+-------+',
            '',
        ];
        $result = $textGrid->render();
        $lines = explode(PHP_EOL, $result);
        self::assertSame($expected, $lines);
        $spreadsheet->disconnectWorksheets();
    }
}
