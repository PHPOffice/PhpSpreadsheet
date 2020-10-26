<?php

namespace PhpOffice\PhpSpreadsheetTests\Style;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

class StyleTest extends TestCase
{
    public function testStyleOddMethods(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $cellCoordinate = 'A1';
        $cell1 = $sheet->getCell($cellCoordinate);
        $cell1style = $cell1->getStyle();
        self::assertSame($spreadsheet, $cell1style->getParent());
        $styleArray = ['alignment' => ['textRotation' => 45]];
        $outArray = $cell1style->getStyleArray($styleArray);
        self::assertEquals($styleArray, $outArray['quotePrefix']);
    }
}
