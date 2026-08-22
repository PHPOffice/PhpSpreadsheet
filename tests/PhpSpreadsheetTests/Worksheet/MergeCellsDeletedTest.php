<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Worksheet;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PHPUnit\Framework\TestCase;

class MergeCellsDeletedTest extends TestCase
{
    public function testDeletedColumns(): void
    {
        $infile = 'tests/data/Reader/XLSX/issue.282.xlsx';
        $reader = new XlsxReader();
        $spreadsheet = $reader->load($infile);
        $sheet = $spreadsheet->getSheetByNameOrThrow('Sheet1');

        $mergeCells = $sheet->getMergeCells();
        self::assertSame(['B1:F1', 'G1:I1'], array_values($mergeCells));

        // Want to delete column B,C,D,E,F
        $sheet->removeColumnByIndex(2, 5);
        $mergeCells2 = $sheet->getMergeCells();
        self::assertSame(['B1:D1'], array_values($mergeCells2));
        $spreadsheet->disconnectWorksheets();
    }

    public function testDeletedRows(): void
    {
        $infile = 'tests/data/Reader/XLSX/issue.282.xlsx';
        $reader = new XlsxReader();
        $spreadsheet = $reader->load($infile);
        $sheet = $spreadsheet->getSheetByNameOrThrow('Sheet2');

        $mergeCells = $sheet->getMergeCells();
        self::assertSame(['A2:A6', 'A7:A9'], array_values($mergeCells));

        // Want to delete rows 2 to 4
        $sheet->removeRow(2, 3);
        $mergeCells2 = $sheet->getMergeCells();
        self::assertSame(['A4:A6'], array_values($mergeCells2));
        $spreadsheet->disconnectWorksheets();
    }

    private static function yellowBackground(Worksheet $sheet, string $cells, string $color = 'ffffff00'): void
    {
        $sheet->getStyle($cells)
            ->getFill()
            ->setFillType(Fill::FILL_SOLID);
        $sheet->getStyle($cells)
            ->getFill()
            ->getStartColor()
            ->setArgb($color);
        $sheet->getStyle($cells)
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);
    }

    public static function testDeletedColumns2(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Before');
        $sheet->getCell('A1')->setValue('a1');
        $sheet->getCell('J1')->setValue('j1');
        $sheet->getCell('K1')->setValue('will delete d-f');
        $sheet->getCell('C1')->setValue('c1-g1');
        $sheet->mergeCells('C1:G1');
        self::yellowBackground($sheet, 'C1');

        $sheet->getCell('A2')->setValue('a2');
        $sheet->getCell('J2')->setValue('j2');
        $sheet->getCell('B2')->setValue('b2-c2');
        $sheet->mergeCells('B2:C2');
        self::yellowBackground($sheet, 'B2');
        $sheet->getCell('G2')->setValue('g2-h2');
        $sheet->mergeCells('G2:H2');
        self::yellowBackground($sheet, 'G2', 'FF00FFFF');

        $sheet->getCell('A3')->setValue('a3');
        $sheet->getCell('J3')->setValue('j3');
        $sheet->getCell('D3')->setValue('d3-g3');
        $sheet->mergeCells('D3:G3');
        self::yellowBackground($sheet, 'D3');

        $sheet->getCell('A4')->setValue('a4');
        $sheet->getCell('J4')->setValue('j4');
        $sheet->getCell('B4')->setValue('b4-d4');
        $sheet->mergeCells('B4:D4');
        self::yellowBackground($sheet, 'B4');

        $sheet->getCell('A5')->setValue('a5');
        $sheet->getCell('J5')->setValue('j5');
        $sheet->getCell('D5')->setValue('d5-e5');
        $sheet->mergeCells('D5:E5');
        self::yellowBackground($sheet, 'D5');

        $sheet->removeColumn('D', 3);
        $expected = [
            'C1:D1', // was C1:G1, drop 3 inside cells
            'B2:C2', // was B2:C2, unaffected
            'D2:E2', // was G2:H2, move 3 columns left
            //'D2:E2', // was D3:G3, start in delete range
            'B4:C4', // was B4:D4, truncated at start of delete range
            //'D5:E5', // was D5:E5, start in delete range
        ];
        self::assertSame($expected, array_keys($sheet->getMergeCells()));

        $spreadsheet->disconnectWorksheets();
    }

    public static function testDeletedRows2(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Before');
        $sheet->getCell('A1')->setValue('a1');
        $sheet->getCell('A10')->setValue('a10');
        $sheet->getCell('A11')->setValue('will delete 4-6');
        $sheet->getCell('A3')->setValue('a3-a7');
        $sheet->mergeCells('A3:A7');
        self::yellowBackground($sheet, 'A3');

        $sheet->getCell('B1')->setValue('b1');
        $sheet->getCell('B10')->setValue('b10');
        $sheet->getCell('B2')->setValue('b2-b3');
        $sheet->mergeCells('B2:B3');
        self::yellowBackground($sheet, 'B2');
        $sheet->getCell('B7')->setValue('b7-b8');
        $sheet->mergeCells('B7:B8');
        self::yellowBackground($sheet, 'B7', 'FF00FFFF');

        $sheet->getCell('C1')->setValue('c1');
        $sheet->getCell('C10')->setValue('c10');
        $sheet->getCell('C4')->setValue('c4-c7');
        $sheet->mergeCells('C4:C7');
        self::yellowBackground($sheet, 'C4');

        $sheet->getCell('D1')->setValue('d1');
        $sheet->getCell('D10')->setValue('d10');
        $sheet->getCell('D2')->setValue('d2-d4');
        $sheet->mergeCells('D2:D4');
        self::yellowBackground($sheet, 'd2');

        $sheet->getCell('E1')->setValue('e1');
        $sheet->getCell('E10')->setValue('e10');
        $sheet->getCell('E4')->setValue('e4-e5');
        $sheet->mergeCells('E4:E5');
        self::yellowBackground($sheet, 'E4');

        $sheet->removeRow(4, 3);
        $expected = [
            'A3:A4', // was A3:A7, drop 3 inside cells
            'B2:B3', // was B2:B3, unaffected
            'B4:B5', // was B7:B8, move 3 columns up
            //'C4:C7', // was C4:C7, start in delete range
            'D2:D3', // was D2:D4, truncated at start of delete range
            //'E4:E5', // was E4:E5, start in delete range
        ];
        self::assertSame($expected, array_keys($sheet->getMergeCells()));

        $spreadsheet->disconnectWorksheets();
    }
}
