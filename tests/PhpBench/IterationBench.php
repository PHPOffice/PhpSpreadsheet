<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetPhpBench;

use PhpBench\Attributes as Bench;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

#[Bench\BeforeMethods('setUp')]
#[Bench\Groups(['iteration'])]
class IterationBench
{
    private Worksheet $sheet;

    public function setUp(): void
    {
        $this->sheet = SheetFactory::create(2000, 20)->getActiveSheet();
    }

    public function benchToArray(): void
    {
        $this->sheet->toArray();
    }

    public function benchRangeToArray(): void
    {
        $this->sheet->rangeToArray('A1:T2000', null, false, false);
    }

    public function benchRowCellIterator(): void
    {
        foreach ($this->sheet->getRowIterator() as $row) {
            foreach ($row->getCellIterator() as $cell) {
                $cell->getValue();
            }
        }
    }

    public function benchHighestRowAndColumn(): void
    {
        for ($i = 0; $i < 100; ++$i) {
            $this->sheet->getHighestDataRow();
            $this->sheet->getHighestDataColumn();
        }
    }
}
