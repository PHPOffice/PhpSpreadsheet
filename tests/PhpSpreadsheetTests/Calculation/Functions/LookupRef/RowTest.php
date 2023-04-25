<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\LookupRef;

use PhpOffice\PhpSpreadsheet\Calculation\LookupRef;

class RowTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerROW
     *
     * @param mixed $expectedResult
     * @param null|array|string $cellReference
     */
    public function testROW($expectedResult, $cellReference = null): void
    {
        $result = LookupRef\RowColumnInformation::ROW($cellReference);
        self::assertSame($expectedResult, $result);
    }

    public static function providerROW(): array
    {
        return require 'tests/data/Calculation/LookupRef/ROW.php';
    }

    public function testROWwithNull(): void
    {
        $sheet = $this->getSheet();
        $sheet->getCell('C4')->setValue('=ROW()');
        self::assertSame(4, $sheet->getCell('C4')->getCalculatedValue());
        $sheet->getCell('D2')->setValue('=ROW(C13)');
        self::assertSame(13, $sheet->getCell('D2')->getCalculatedValue());
        // Sheetnames don't have to exist
        $sheet->getCell('D3')->setValue('=ROW(Sheet17!E15)');
        self::assertSame(15, $sheet->getCell('D3')->getCalculatedValue());
        $sheet->getCell('D4')->setValue("=ROW('Worksheet #5'!X500)");
        self::assertSame(500, $sheet->getCell('D4')->getCalculatedValue());
    }
}
