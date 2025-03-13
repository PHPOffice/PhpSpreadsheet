<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\Spreadsheet;

class MySpreadsheet extends Spreadsheet
{
    public function calcSquare(string $cellAddress): float|int|string
    {
        $value = $this->getActiveSheet()
            ->getCell($cellAddress)
            ->getValue();
        if (is_numeric($value)) {
            return $value * $value;
        }

        return '#VALUE!';
    }
}
