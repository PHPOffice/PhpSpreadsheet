<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\LookupRef;

use PhpOffice\PhpSpreadsheet\NamedRange;

class RowsOnSpreadsheetTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerROWSonSpreadsheet
     *
     * @param mixed $expectedResult
     * @param string $cellReference
     */
    public function testRowsOnSpreadsheet($expectedResult, $cellReference = 'omitted'): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->sheet;
        $this->spreadsheet->addNamedRange(new NamedRange('namedrangex', $sheet, '$E$2:$E$6'));
        $this->spreadsheet->addNamedRange(new NamedRange('namedrangey', $sheet, '$F$2:$H$2'));
        $this->spreadsheet->addNamedRange(new NamedRange('namedrange3', $sheet, '$F$4:$H$4'));

        $sheet1 = $this->spreadsheet->createSheet();
        $sheet1->setTitle('OtherSheet');

        if ($cellReference === 'omitted') {
            $sheet->getCell('B3')->setValue('=ROWS()');
        } else {
            $sheet->getCell('B3')->setValue("=ROWS($cellReference)");
        }

        $result = $sheet->getCell('B3')->getCalculatedValue();
        self::assertSame($expectedResult, $result);
    }

    public function providerROWSOnSpreadsheet()
    {
        return require 'tests/data/Calculation/LookupRef/ROWSonSpreadsheet.php';
    }

    public function testRowsLocalDefinedName(): void
    {
        $sheet = $this->sheet;

        $sheet1 = $this->spreadsheet->createSheet();
        $sheet1->setTitle('OtherSheet');
        $this->spreadsheet->addNamedRange(new NamedRange('newnr', $sheet1, '$F$5:$H$10', true)); // defined locally, only usable on sheet1

        $sheet1->getCell('B3')->setValue('=ROWS(newnr)');
        $result = $sheet1->getCell('B3')->getCalculatedValue();
        self::assertSame(6, $result);

        $sheet->getCell('B3')->setValue('=ROWS(newnr)');
        $result = $sheet->getCell('B3')->getCalculatedValue();
        self::assertSame('#NAME?', $result);
    }
}
