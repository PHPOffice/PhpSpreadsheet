<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetPhpBench;

use PhpBench\Attributes as Bench;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Reader\Xls as XlsReader;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xls as XlsWriter;

#[Bench\BeforeClassMethods('createFixtures')]
#[Bench\Groups(['reader'])]
class XlsReaderBench
{
    private const REAL_FIXTURES = [
        'biff8cover.xls',
        'formulas.xls',
        'RichTextFontSize.xls',
        'Colours.xls',
        'PageSetup.xls',
        'DataValidation.xls',
    ];

    public static function createFixtures(): void
    {
        if (!is_file(self::manyCellsFile())) {
            self::saveManyCells(self::manyCellsFile());
        }
        if (!is_file(self::sharedStringsFile())) {
            self::saveSharedStrings(self::sharedStringsFile());
        }
    }

    /**
     * Real files exercising rich text, formulas, conditional formatting and data validation.
     */
    public function benchRealFixtures(): void
    {
        $reader = new XlsReader();
        foreach (self::REAL_FIXTURES as $fixture) {
            $reader->load(__DIR__ . '/../data/Reader/XLS/' . $fixture)->disconnectWorksheets();
        }
    }

    /**
     * Per-cell overhead: 3 sheets of 500 x 8 numbers with styles and SUM formulas.
     */
    public function benchManyCells(): void
    {
        (new XlsReader())->load(self::manyCellsFile());
    }

    /**
     * Shared string table parsing: 1000 x 10 distinct strings.
     */
    public function benchSharedStrings(): void
    {
        (new XlsReader())->load(self::sharedStringsFile());
    }

    private static function manyCellsFile(): string
    {
        return SheetFactory::fixtureDirectory() . '/xls-many-cells.xls';
    }

    private static function sharedStringsFile(): string
    {
        return SheetFactory::fixtureDirectory() . '/xls-shared-strings.xls';
    }

    private static function saveManyCells(string $file): void
    {
        $spreadsheet = new Spreadsheet();
        $columns = 8;
        $lastDataColumn = Coordinate::stringFromColumnIndex($columns - 1);
        $sumColumn = Coordinate::stringFromColumnIndex($columns);
        for ($s = 0; $s < 3; ++$s) {
            $sheet = $s === 0 ? $spreadsheet->getActiveSheet() : $spreadsheet->createSheet();
            $sheet->setTitle("Sheet{$s}");
            for ($row = 1; $row <= 500; ++$row) {
                for ($col = 1; $col < $columns; ++$col) {
                    $sheet->getCell([$col, $row])->setValue($row * $col + $s * 1000);
                }
                $sheet->getCell("{$sumColumn}{$row}")->setValue($row === 1 ? $s * 1000 : "=SUM(A{$row}:{$lastDataColumn}{$row})");
                if ($row % 5 === 0) {
                    $sheet->getStyle("A{$row}")->applyFromArray([
                        'font' => ['bold' => true, 'color' => ['argb' => 'FF003366']],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFD9E2F3']],
                        'borders' => ['bottom' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => Color::COLOR_BLACK]]],
                    ]);
                }
            }
        }
        (new XlsWriter($spreadsheet))->save($file);
    }

    private static function saveSharedStrings(string $file): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        for ($row = 1; $row <= 1000; ++$row) {
            for ($col = 1; $col <= 10; ++$col) {
                $sheet->getCell([$col, $row])->setValue("StringValue_R{$row}_C{$col}_" . str_repeat('x', ($row + $col) % 20 + 5));
            }
        }
        (new XlsWriter($spreadsheet))->save($file);
    }
}
