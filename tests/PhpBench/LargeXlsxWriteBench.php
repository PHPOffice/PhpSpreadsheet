<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetPhpBench;

use PhpBench\Attributes as Bench;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

#[Bench\BeforeMethods('setUp')]
#[Bench\AfterMethods('tearDown')]
#[Bench\Groups(['writer'])]
class LargeXlsxWriteBench
{
    private string $file;

    public function setUp(): void
    {
        $this->file = SheetFactory::tempFile('xlsx');
    }

    public function tearDown(): void
    {
        if (is_file($this->file)) {
            unlink($this->file);
        }
    }

    /**
     * Populates 2000 x 20 text cells one by one, then saves without precalculating formulas.
     */
    public function benchPopulateAndSave(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        for ($row = 1; $row <= 2000; ++$row) {
            for ($col = 1; $col <= 20; ++$col) {
                $sheet->getCell([$col, $row])->setValue("R{$row}C{$col}");
            }
        }

        $writer = new Xlsx($spreadsheet);
        $writer->setPreCalculateFormulas(false);
        $writer->save($this->file);
    }
}
