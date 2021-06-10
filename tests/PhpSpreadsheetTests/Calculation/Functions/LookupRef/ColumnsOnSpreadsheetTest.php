<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\LookupRef;

use PhpOffice\PhpSpreadsheet\NamedRange;

class ColumnsOnSpreadsheetTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerCOLUMNSonSpreadsheet
     *
     * @param mixed $expectedResult
     * @param string $cellReference
     */
    public function testColumnsOnSpreadsheet($expectedResult, $cellReference = 'omitted'): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        $sheet->setTitle('ThisSheet');
        $this->getSpreadsheet()->addNamedRange(new NamedRange('namedrangex', $sheet, '$E$2:$E$6'));
        $this->getSpreadsheet()->addNamedRange(new NamedRange('namedrangey', $sheet, '$F$2:$H$2'));
        $this->getSpreadsheet()->addNamedRange(new NamedRange('namedrange3', $sheet, '$F$4:$H$4'));
        $this->getSpreadsheet()->addNamedRange(new NamedRange('namedrange5', $sheet, '$F$5:$I$5', true));

        $sheet1 = $this->getSpreadsheet()->createSheet();
        $sheet1->setTitle('OtherSheet');
        $this->getSpreadsheet()->addNamedRange(new NamedRange('localname', $sheet1, '$F$6:$H$6', true));

        if ($cellReference === 'omitted') {
            $sheet->getCell('B3')->setValue('=COLUMNS()');
        } else {
            $sheet->getCell('B3')->setValue("=COLUMNS($cellReference)");
        }

        $result = $sheet->getCell('B3')->getCalculatedValue();
        self::assertSame($expectedResult, $result);
    }

    public function providerCOLUMNSonSpreadsheet(): array
    {
        return require 'tests/data/Calculation/LookupRef/COLUMNSonSpreadsheet.php';
    }

    public function testCOLUMNSLocalDefinedName(): void
    {
        $sheet = $this->getSheet();

        $sheet1 = $this->getSpreadsheet()->createSheet();
        $sheet1->setTitle('OtherSheet');
        $this->getSpreadsheet()->addNamedRange(new NamedRange('newnr', $sheet1, '$F$5:$H$5', true)); // defined locally, only usable on sheet1

        $sheet1->getCell('B3')->setValue('=COLUMNS(newnr)');
        $result = $sheet1->getCell('B3')->getCalculatedValue();
        self::assertSame(3, $result);

        $sheet->getCell('B3')->setValue('=COLUMNS(newnr)');
        $result = $sheet->getCell('B3')->getCalculatedValue();
        self::assertSame('#NAME?', $result);
    }
}
