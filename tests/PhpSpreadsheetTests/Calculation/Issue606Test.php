<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

class Issue606Test extends TestCase
{
    public function testIssue606(): void
    {
        $spreadSheet = new Spreadsheet();
        $sheet = $spreadSheet->getActiveSheet();
        // some dummy data
        $sheet->getCell('A4')->setValue(2);
        $sheet->getCell('A5')->setValue(2);
        $sheet->getCell('A6')->setValue(3);

        $sheet->getCell('A1')->setValue('=FREQUENCY(A4:A6,A4:A6)');
        $sheet->getCell('A1')->setCalculatedValue(123);
        self::assertSame(
            123,
            $sheet->getCell('A1')->getCalculatedValue(),
            'unimplemented function uses old calculated value'
        );

        $sheet->getCell('A2')
            ->setValue('=SUM(FREQUENCY(A4:A6,A4:A6))');
        $sheet->getCell('A2')->setCalculatedValue(124);
        self::assertSame(
            124,
            $sheet->getCell('A2')->getCalculatedValue(),
            'unimplemented propagates so use old calculated value'
        );

        $sheet->getCell('A3')->setValue(
            '=SUM(IF(FREQUENCY(IF(SUBTOTAL(3, OFFSET(A4:A6,ROW(A4:A6)-ROW(A4),0,1)), MATCH(A4:A6,A4:A6,0)), ROW(A4:A6)-ROW(A4)+1)>0,1))'
        );
        $sheet->getCell('A3')->setCalculatedValue(125);
        self::assertSame(
            125,
            $sheet->getCell('A3')->getCalculatedValue(),
            'unimplemented propagates to comparison then IF so use old calculated value'
        );

        $sheet->getCell('D1')->setValue(3);
        $sheet->getCell('D2')->setValue(0);
        $sheet->getCell('E1')->setCalculatedValue(126);
        $sheet->getCell('E1')
            ->setValue('=IF(0 > D1/D2, 0, 1)');
        self::assertSame(
            '#DIV/0!',
            $sheet->getCell('E1')->getCalculatedValue(),
            'DIV0 propagates to comparison then IF, no need for old calculated value'
        );

        $sheet->getCell('D5')
            ->setValue('=MAX(1, FREQUENCY(1,2), 2)');
        $sheet->getCell('D6')
            ->setValue('=MAXA(1, FREQUENCY(1,2), 2)');
        $sheet->getCell('D7')
            ->setValue('=MIN(1, FREQUENCY(1,2), 2)');
        $sheet->getCell('D8')
            ->setValue('=MINA(1, FREQUENCY(1,2), 2)');
        self::assertNull($sheet->getCell('D5')->getCalculatedValue());
        self::assertNull($sheet->getCell('D6')->getCalculatedValue());
        self::assertNull($sheet->getCell('D7')->getCalculatedValue());
        self::assertNull($sheet->getCell('D8')->getCalculatedValue());

        $sheet->getCell('F1')->setValue(1);
        $sheet->getCell('F2')->setValue(3);
        $sheet->getCell('F3')->setValue(2);
        $sheet->getCell('F5')->setValue('=SORT(F1:F3, SQRT(-1))');
        self::assertSame('#NUM!', $sheet->getCell('F5')->getCalculatedValue());
        $sheet->getCell('F7')->setValue('=SORT(F1:F3, FREQUENCY(1, 2))');
        self::assertNull($sheet->getCell('F7')->getCalculatedValue());

        $sheet->getCell('H1')->setValue('=ROWS(FREQUENCY(1,2))');
        $sheet->getCell('H2')->setValue('=COLUMNS(FREQUENCY(1,2))');
        self::assertNull($sheet->getCell('H1')->getCalculatedValue());
        self::assertNull($sheet->getCell('H2')->getCalculatedValue());

        $spreadSheet->disconnectWorksheets();
    }
}
