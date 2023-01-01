<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation;

use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

class StringLengthTest extends TestCase
{
    public function testStringLength(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        // Note use Armenian character below to make sure chars, not bytes
        $longstring = str_repeat('Ԁ', DataType::MAX_STRING_LENGTH - 5);
        $sheet->getCell('C1')->setValue($longstring);
        self::assertSame($longstring, $sheet->getCell('C1')->getValue());
        $sheet->getCell('C2')->setValue($longstring . 'abcdef');
        self::assertSame($longstring . 'abcde', $sheet->getCell('C2')->getValue());
        $sheet->getCell('C3')->setValue('abcdef');
        $sheet->getCell('C4')->setValue('=C1 & C3');
        self::assertSame($longstring . 'abcde', $sheet->getCell('C4')->getCalculatedValue(), 'truncate cell concat with cell');
        $sheet->getCell('C5')->setValue('=C1 & "A"');
        self::assertSame($longstring . 'A', $sheet->getCell('C5')->getCalculatedValue(), 'okay cell concat with literal');
        $sheet->getCell('C6')->setValue('=C1 & "ABCDEF"');
        self::assertSame($longstring . 'ABCDE', $sheet->getCell('C6')->getCalculatedValue(), 'truncate cell concat with literal');
        $sheet->getCell('C7')->setValue('="ABCDEF" & C1');
        self::assertSame('ABCDEF' . str_repeat('Ԁ', DataType::MAX_STRING_LENGTH - 6), $sheet->getCell('C7')->getCalculatedValue(), 'truncate literal concat with cell');
        $sheet->getCell('C8')->setValue('="ABCDE" & C1');
        self::assertSame('ABCDE' . $longstring, $sheet->getCell('C8')->getCalculatedValue(), 'okay literal concat with cell');
        $sheet->getCell('C9')->setValue('=false & true & 3');
        self::assertSame('FALSETRUE3', $sheet->getCell('C9')->getCalculatedValue());
        $sheet->getCell('D8')->setValue('abcde');
        $sheet->getCell('D9')->setValue('=D8 & "*" & D8');
        self::assertSame('abcde*abcde', $sheet->getCell('D9')->getCalculatedValue());
        $sheet->getCell('E8')->setValue('"abcde"');
        $sheet->getCell('E9')->setValue('=E8 & "*" & E8');
        self::assertSame('"abcde"*"abcde"', $sheet->getCell('E9')->getCalculatedValue());
        $sheet->getCell('F8')->setValue('"abcde"');
        $sheet->getCell('F9')->setValue('=F8 & "*" & "abcde"');
        self::assertSame('"abcde"*abcde', $sheet->getCell('F9')->getCalculatedValue());
        $spreadsheet->disconnectWorksheets();
    }
}
