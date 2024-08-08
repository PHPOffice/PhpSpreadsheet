<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Information;

use PhpOffice\PhpSpreadsheet\NamedRange;
use PhpOffice\PhpSpreadsheetTests\Calculation\Functions\LookupRef\AllSetupTeardown;

class IsRefTest extends AllSetupTeardown
{
    private bool $skipA13 = true;

    public function testIsRef(): void
    {
        $sheet = $this->getSheet();

        $sheet->getParentOrThrow()->addDefinedName(new NamedRange('NAMED_RANGE', $sheet, 'C1'));

        $sheet->getCell('A1')->setValue('=ISREF(B1)');
        $sheet->getCell('A2')->setValue('=ISREF(B1:B2)');
        $sheet->getCell('A3')->setValue('=ISREF(B1:D4 C1:C5)');
        $sheet->getCell('A4')->setValue('=ISREF("PHP")');
        $sheet->getCell('A5')->setValue('=ISREF(B1*B2)');
        $sheet->getCell('A6')->setValue('=ISREF(Worksheet2!B1)');
        $sheet->getCell('A7')->setValue('=ISREF(NAMED_RANGE)');
        $sheet->getCell('A8')->setValue('=ISREF(INDIRECT("' . $sheet->getTitle() . '" & "!" & "A1"))');
        $sheet->getCell('A9')->setValue('=ISREF(INDIRECT("A1"))');
        $sheet->getCell('A10')->setValue('=ISREF(INDIRECT("Invalid Worksheet" & "!" & "A1"))');
        $sheet->getCell('A11')->setValue('=ISREF(INDIRECT("Invalid Worksheet" & "!A1"))');
        $sheet->getCell('A12')->setValue('=ISREF(ZZZ1)');
        $sheet->getCell('A13')->setValue('=ISREF(CHOOSE(2, A1, B1, C1))');

        self::assertTrue($sheet->getCell('A1')->getCalculatedValue()); // Cell Reference
        self::assertTrue($sheet->getCell('A2')->getCalculatedValue()); // Cell Range
        self::assertTrue($sheet->getCell('A3')->getCalculatedValue()); // Complex Cell Range
        self::assertFalse($sheet->getCell('A4')->getCalculatedValue()); // Text String
        self::assertFalse($sheet->getCell('A5')->getCalculatedValue()); // Result of a math expression
        self::assertTrue($sheet->getCell('A6')->getCalculatedValue()); // Cell Reference with worksheet
        self::assertTrue($sheet->getCell('A7')->getCalculatedValue()); // Named Range
        self::assertTrue($sheet->getCell('A8')->getCalculatedValue()); // Indirect to a Cell Reference
        self::assertTrue($sheet->getCell('A9')->getCalculatedValue()); // Indirect to a Worksheet/Cell Reference
        self::assertFalse($sheet->getCell('A10')->getCalculatedValue()); // Indirect to an Invalid Worksheet/Cell Reference
        self::assertFalse($sheet->getCell('A11')->getCalculatedValue()); // Indirect to an Invalid Worksheet/Cell Reference
        self::assertFalse($sheet->getCell('A12')->getCalculatedValue()); // Invalid Cell Reference
        if ($this->skipA13) {
            self::markTestIncomplete('Calculation for A13 is too complicated');
        }
        self::assertTrue($sheet->getCell('A13')->getCalculatedValue()); // returned Cell Reference
    }
}
