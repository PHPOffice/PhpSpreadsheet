<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetPhpBench;

use PhpBench\Attributes as Bench;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;

/**
 * Compares clone with Spreadsheet::copy() (serialize/unserialize).
 */
#[Bench\BeforeMethods('setUp')]
#[Bench\Groups(['copy'])]
class SpreadsheetCopyBench
{
    private Spreadsheet $spreadsheet;

    public function setUp(): void
    {
        $spreadsheet = new Spreadsheet();
        $columns = ['A', 'B', 'C', 'D', 'E'];

        $dataSheet = $spreadsheet->getActiveSheet()->setTitle('Data');
        for ($row = 1; $row <= 200; ++$row) {
            foreach ($columns as $index => $column) {
                $dataSheet->getCell("{$column}{$row}")->setValue("Data R{$row}C" . ($index + 1));
            }
            $dataSheet->getStyle("A{$row}")->applyFromArray([
                'font' => ['bold' => $row % 2 === 0, 'color' => ['argb' => 'FF003366'], 'size' => 11],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $row % 3 === 0 ? 'FFFFF2CC' : 'FFD9E2F3']],
                'borders' => ['bottom' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => Color::COLOR_BLACK]]],
            ]);
        }

        $numericSheet = $spreadsheet->createSheet()->setTitle('Numbers');
        for ($row = 1; $row <= 200; ++$row) {
            foreach ($columns as $index => $column) {
                $numericSheet->getCell("{$column}{$row}")->setValue($row * ($index + 1) * 1.5);
            }
        }

        $formulaSheet = $spreadsheet->createSheet()->setTitle('Formulas');
        for ($row = 1; $row <= 200; ++$row) {
            $formulaSheet->getCell("A{$row}")->setValue("=Numbers!A{$row}+Numbers!B{$row}");
            $formulaSheet->getCell("B{$row}")->setValue("=Numbers!C{$row}*2");
            $formulaSheet->getCell("C{$row}")->setValue("=SUM(Numbers!A{$row}:Numbers!E{$row})");
        }

        $mixedSheet = $spreadsheet->createSheet()->setTitle('Mixed');
        for ($row = 1; $row <= 100; ++$row) {
            $mixedSheet->getCell("A{$row}")->setValue("Label {$row}");
            $mixedSheet->getCell("B{$row}")->setValue($row * 100);
            $mixedSheet->getCell("C{$row}")->setValue($row % 2 === 0);
            $mixedSheet->getCell("D{$row}")->setValue(date('Y-m-d', (int) strtotime("2024-01-01 +{$row} days")));
        }
        $mixedSheet->getColumnDimension('A')->setWidth(20);
        $mixedSheet->getColumnDimension('B')->setWidth(15);

        $this->spreadsheet = $spreadsheet;
    }

    public function benchClone(): void
    {
        $copy = clone $this->spreadsheet;
        $copy->disconnectWorksheets();
    }

    public function benchCopy(): void
    {
        $copy = $this->spreadsheet->copy();
        $copy->disconnectWorksheets();
    }
}
