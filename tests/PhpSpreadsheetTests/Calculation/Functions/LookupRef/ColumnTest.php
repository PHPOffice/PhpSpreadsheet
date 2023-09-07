<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\LookupRef;

use PhpOffice\PhpSpreadsheet\Calculation\LookupRef;

class ColumnTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerCOLUMN
     */
    public function testCOLUMN(mixed $expectedResult, null|array|string $cellReference = null): void
    {
        $result = LookupRef\RowColumnInformation::COLUMN($cellReference);
        self::assertSame($expectedResult, $result);
    }

    public static function providerCOLUMN(): array
    {
        return require 'tests/data/Calculation/LookupRef/COLUMN.php';
    }

    public function testCOLUMNwithNull(): void
    {
        $sheet = $this->getSheet();
        $sheet->getCell('D1')->setValue('=COLUMN()');
        self::assertSame(4, $sheet->getCell('D1')->getCalculatedValue());
        $sheet->getCell('D2')->setValue('=COLUMN(C13)');
        self::assertSame(3, $sheet->getCell('D2')->getCalculatedValue());
        // Sheetnames don't have to exist
        $sheet->getCell('D3')->setValue('=COLUMN(Sheet17!E15)');
        self::assertSame(5, $sheet->getCell('D3')->getCalculatedValue());
        $sheet->getCell('D4')->setValue("=COLUMN('Worksheet #5'!X500)");
        self::assertSame(24, $sheet->getCell('D4')->getCalculatedValue());
    }
}
