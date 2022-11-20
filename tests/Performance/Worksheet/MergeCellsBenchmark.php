<?php

namespace PhpOffice\PhpSpreadsheetPerformance\Worksheet;

use PhpOffice\PhpSpreadsheetPerformance\ReferenceHelper\AbstractInsertDelete;

class MergeCellsBenchmark extends AbstractInsertDelete
{
    public function __construct()
    {
        parent::__construct(256, 512);
    }

    /**
     * @revs(5)
     * @Iterations(12)
     * @OutputTimeUnit("milliseconds")
     * @AfterMethods("tearDown")
     */
    public function benchmarkMergeCellsByArray(): void
    {
        for ($row = 1; $row <= 512; $row += 4) {
            for ($column = 1; $column <= 256; $column += 4) {
                $this->spreadsheet->getActiveSheet()->mergeCells([$column, $row, $column + 2, $row + 2]);
            }
        }
    }
}
