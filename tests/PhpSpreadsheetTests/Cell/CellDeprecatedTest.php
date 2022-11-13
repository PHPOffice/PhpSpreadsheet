<?php

namespace PhpOffice\PhpSpreadsheetTests\Cell;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

class CellDeprecatedTest extends TestCase
{
    public function testByColumnAndRow(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getCell('D2')->setValue('abc');
        self::assertSame('abc', /** @scrutinizer ignore-deprecated */ $sheet->getCellByColumnAndRow(4, 2)->getValue());
        self::assertSame('abc', $sheet->getCell([4, 2])->getValue());

        $spreadsheet->disconnectWorksheets();
    }
}
