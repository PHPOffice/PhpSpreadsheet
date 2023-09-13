<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Style;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PHPUnit\Framework\TestCase;

class NumberFormatBuiltinTest extends TestCase
{
    public function testBuiltinCodes(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $cell1 = $sheet->getCell('A1');
        $cell1->setValue(1);
        $cell1->getStyle()->getNumberFormat()->setBuiltInFormatCode(2); // 0.00
        self::assertEquals('1.00', $cell1->getFormattedValue());
        $cell2 = $sheet->getCell('A2');
        $cell2->setValue(1234);
        $cell2->getStyle()->getNumberFormat()->setFormatCode('#,##0'); // builtin 3
        self::assertEquals(3, $cell2->getStyle()->getNumberFormat()->getBuiltinFormatCode());
        self::assertEquals('1,234', $cell2->getFormattedValue());
        $cell3 = $sheet->getCell('A3');
        $cell3->setValue(1234);
        $cell3->getStyle()->getNumberFormat()->setFormatCode(''); // General
        self::assertEquals(NumberFormat::FORMAT_GENERAL, $cell3->getStyle()->getNumberFormat()->getFormatCode());
        self::assertEquals(0, $cell3->getStyle()->getNumberFormat()->getBuiltinFormatCode());
        self::assertEquals('1234', $cell3->getFormattedValue());
        // non-supervisor
        $numberFormat = new NumberFormat();
        $numberFormat->setBuiltInFormatCode(4);
        self::assertEquals('#,##0.00', $numberFormat->getFormatCode());
    }
}
