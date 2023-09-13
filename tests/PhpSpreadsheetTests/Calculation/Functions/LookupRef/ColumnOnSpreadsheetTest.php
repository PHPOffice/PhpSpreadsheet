<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\LookupRef;

use PhpOffice\PhpSpreadsheet\NamedRange;

class ColumnOnSpreadsheetTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerCOLUMNonSpreadsheet
     */
    public function testColumnOnSpreadsheet(mixed $expectedResult, string $cellReference = 'omitted'): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        $this->getSpreadsheet()->addNamedRange(new NamedRange('namedrangex', $sheet, '$E$2:$E$6'));
        $this->getSpreadsheet()->addNamedRange(new NamedRange('namedrangey', $sheet, '$F$2:$H$2'));
        $this->getSpreadsheet()->addNamedRange(new NamedRange('namedrange3', $sheet, '$F$4:$H$4'));

        $sheet1 = $this->getSpreadsheet()->createSheet();
        $sheet1->setTitle('OtherSheet');

        if ($cellReference === 'omitted') {
            $sheet->getCell('B3')->setValue('=COLUMN()');
        } else {
            $sheet->getCell('B3')->setValue("=COLUMN($cellReference)");
        }

        $result = $sheet->getCell('B3')->getCalculatedValue();
        self::assertSame($expectedResult, $result);
    }

    public static function providerCOLUMNonSpreadsheet(): array
    {
        return require 'tests/data/Calculation/LookupRef/COLUMNonSpreadsheet.php';
    }

    public function testCOLUMNLocalDefinedName(): void
    {
        $sheet = $this->getSheet();

        $sheet1 = $this->getSpreadsheet()->createSheet();
        $sheet1->setTitle('OtherSheet');
        $this->getSpreadsheet()->addNamedRange(new NamedRange('newnr', $sheet1, '$F$5:$H$5', true)); // defined locally, only usable on sheet1

        $sheet1->getCell('B3')->setValue('=COLUMN(newnr)');
        $result = $sheet1->getCell('B3')->getCalculatedValue();
        self::assertSame(6, $result);

        $sheet->getCell('B3')->setValue('=COLUMN(newnr)');
        $result = $sheet->getCell('B3')->getCalculatedValue();
        self::assertSame('#NAME?', $result);
    }
}
