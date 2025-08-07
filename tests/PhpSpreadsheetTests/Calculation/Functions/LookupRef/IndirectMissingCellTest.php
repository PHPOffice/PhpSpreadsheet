<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\LookupRef;

class IndirectMissingCellTest extends AllSetupTeardown
{
    public function testIssue4330(): void
    {
        $sheet = $this->getSheet();
        $sheet->getCell('A1')->setValue(5); // used in INDIRECT
        $sheet->getCell('A2')->setValue(1);

        $sheet->getCell('B1')->setValue(2);
        $sheet->getCell('B2')->setValue(4);
        $sheet->getCell('B3')->setValue(6);
        $sheet->getCell('B4')->setValue(8);
        $sheet->getCell('B5')->setValue(10);
        $sheet->getCell('B6')->setValue(12);
        $sheet->getCell('C1')->setValue('=SUM(B1:INDIRECT("B"&A1))');
        self::assertSame(
            30,
            $sheet->getCell('C1')->getCalculatedValue(),
            'end cell initialized'
        );

        $sheet->getCell('D1')->setValue(30);
        $sheet->getCell('D2')->setValue(60);
        $sheet->getCell('D3')->setValue(90);
        $sheet->getCell('D4')->setValue(120);
        $sheet->getCell('E1')->setValue('=SUM(D1:INDIRECT("D"&A1))');
        self::assertSame(
            300,
            $sheet->getCell('E1')->getCalculatedValue(),
            'end cell not initialized'
        );

        //$sheet->getCell('F1')->setValue(30);
        //$sheet->getCell('F2')->setValue(60);
        $sheet->getCell('F3')->setValue(90);
        $sheet->getCell('F4')->setValue(120);
        $sheet->getCell('G1')->setValue(
            '=SUM(INDIRECT("F"&A2&":F"&A1))'
        );
        self::assertSame(
            210,
            $sheet->getCell('G1')->getCalculatedValue(),
            'range with uninitialized cells as INDIRECT argument'
        );
    }
}
