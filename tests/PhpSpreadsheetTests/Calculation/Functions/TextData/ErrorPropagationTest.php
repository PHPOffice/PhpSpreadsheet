<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\TextData;

class ErrorPropagationTest extends AllSetupTeardown
{
    public function testErrorPropagation(): void
    {
        $sheet = $this->getSheet();
        $sheet->getCell('A1')->setValue('=ABS("X")');
        self::assertSame('#VALUE!', $sheet->getCell('A1')->getCalculatedValue());
        $sheet->getCell('A2')->setValue('=SQRT(-1)');
        self::assertSame('#NUM!', $sheet->getCell('A2')->getCalculatedValue());
        $sheet->getCell('A3')->setValue('=3/0');
        self::assertSame('#DIV/0!', $sheet->getCell('A3')->getCalculatedValue());
        $sheet->getCell('A4')->setValue('=XXXX()');
        self::assertSame('#NAME?', $sheet->getCell('A4')->getCalculatedValue());
        $sheet->getCell('A5')->setValue('=ABS("X")');
        self::assertSame('#VALUE!', $sheet->getCell('A5')->getCalculatedValue());

        $sheet->getCell('B1')->setValue('=UPPER(A1)');
        self::assertSame('#VALUE!', $sheet->getCell('A1')->getCalculatedValue());
        $sheet->getCell('B2')->setValue('=LOWER(A2)');
        self::assertSame('#NUM!', $sheet->getCell('A2')->getCalculatedValue());
        $sheet->getCell('B3')->setValue('=PROPER(A3)');
        self::assertSame('#DIV/0!', $sheet->getCell('A3')->getCalculatedValue());

        $sheet->getCell('C2')->setValue('=CHAR(A2)');
        self::assertSame('#NUM!', $sheet->getCell('C2')->getCalculatedValue());
        $sheet->getCell('C3')->setValue('=CODE(A3)');
        self::assertSame('#DIV/0!', $sheet->getCell('C3')->getCalculatedValue());

        $sheet->getCell('D1')->setValue('=CONCATENATE(A1,A1)');
        self::assertSame('#VALUE!', $sheet->getCell('D1')->getCalculatedValue());
        $sheet->getCell('D2')->setValue('=TEXTJOIN(",",TRUE,A2,A3)');
        self::assertSame('#NUM!', $sheet->getCell('D2')->getCalculatedValue());
        $sheet->getCell('D3')->setValue('=REPT(A3,3)');
        self::assertSame('#DIV/0!', $sheet->getCell('D3')->getCalculatedValue());
        $sheet->getCell('D4')->setValue('=CONCAT(A4,A4)');
        self::assertSame('#NAME?', $sheet->getCell('D4')->getCalculatedValue());
        $sheet->getCell('D5')->setValue('="X"&A4');
        self::assertSame('#NAME?', $sheet->getCell('D5')->getCalculatedValue());
        $sheet->getCell('D6')->setValue('=A2&"X"');
        self::assertSame('#NUM!', $sheet->getCell('D6')->getCalculatedValue());

        $sheet->getCell('E1')->setValue('=LEFT(A1)');
        self::assertSame('#VALUE!', $sheet->getCell('E1')->getCalculatedValue());
        $sheet->getCell('E2')->setValue('=RIGHT(A2)');
        self::assertSame('#NUM!', $sheet->getCell('E2')->getCalculatedValue());
        $sheet->getCell('E3')->setValue('=MID(A3,2,2)');
        self::assertSame('#DIV/0!', $sheet->getCell('E3')->getCalculatedValue());
        $sheet->getCell('E4')->setValue('=TEXTBEFORE(A4,"M")');
        self::assertSame('#NAME?', $sheet->getCell('E4')->getCalculatedValue());
        $sheet->getCell('E5')->setValue('=TEXTAFTER(A5,"U")');
        self::assertSame('#VALUE!', $sheet->getCell('E5')->getCalculatedValue());

        $sheet->getCell('F1')->setValue('=VALUETOTEXT(A1)');
        self::assertSame('#VALUE!', $sheet->getCell('F1')->getCalculatedValue());
        $sheet->getCell('F2')->setValue('=DOLLAR(A2)');
        self::assertSame('#NUM!', $sheet->getCell('F2')->getCalculatedValue());
        $sheet->getCell('F3')->setValue('=FIXED(A3)');
        self::assertSame('#DIV/0!', $sheet->getCell('E3')->getCalculatedValue());
        $sheet->getCell('F4')->setValue('=TEXT(A4,"M")');
        self::assertSame('#NAME?', $sheet->getCell('F4')->getCalculatedValue());
        $sheet->getCell('F5')->setValue('=VALUE(A2)');
        self::assertSame('#NUM!', $sheet->getCell('F5')->getCalculatedValue());
        $sheet->getCell('F6')->setValue('=NUMBERVALUE(A3)');
        self::assertSame('#DIV/0!', $sheet->getCell('F6')->getCalculatedValue());

        $sheet->getCell('G1')->setValue('=REPLACE("oldtext",2,2,A1)');
        self::assertSame('#VALUE!', $sheet->getCell('G1')->getCalculatedValue());
        $sheet->getCell('G2')->setValue('=SUBSTITUTE(A2,"U","V")');
        self::assertSame('#NUM!', $sheet->getCell('G2')->getCalculatedValue());

        $sheet->getCell('H1')->setValue('=FIND(A1, "U")');
        self::assertSame('#VALUE!', $sheet->getCell('H1')->getCalculatedValue());
        $sheet->getCell('H2')->setValue('=SEARCH(A2,"U")');
        self::assertSame('#NUM!', $sheet->getCell('H2')->getCalculatedValue());

        $sheet->getCell('I1')->setValue('=LEN(A1)');
        self::assertSame('#VALUE!', $sheet->getCell('I1')->getCalculatedValue());
        $sheet->getCell('I2')->setValue('=EXACT(A2,A2)');
        self::assertSame('#NUM!', $sheet->getCell('I2')->getCalculatedValue());
        $sheet->getCell('I3')->setValue('=T(A3)');
        self::assertSame('#DIV/0!', $sheet->getCell('I3')->getCalculatedValue());
        $sheet->getCell('I4')->setValue('=TEXTSPLIT(A4,"M")');
        self::assertSame('#NAME?', $sheet->getCell('I4')->getCalculatedValue());

        $sheet->getCell('J1')->setValue('=TRIM(A1)');
        self::assertSame('#VALUE!', $sheet->getCell('J1')->getCalculatedValue());
        $sheet->getCell('J2')->setValue('=CLEAN(A2)');
        self::assertSame('#NUM!', $sheet->getCell('J2')->getCalculatedValue());
    }
}
