<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\LookupRef;

use PhpOffice\PhpSpreadsheet\NamedRange;

class IndirectTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerINDIRECT
     *
     * @param mixed $expectedResult
     * @param mixed $cellReference
     * @param mixed $a1
     */
    public function testINDIRECT($expectedResult, $cellReference = 'omitted', $a1 = 'omitted'): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->sheet;
        $sheet->getCell('A1')->setValue(100);
        $sheet->getCell('A2')->setValue(200);
        $sheet->getCell('A3')->setValue(300);
        $sheet->getCell('A4')->setValue(400);
        $sheet->getCell('A5')->setValue(500);

        $sheet1 = $this->spreadsheet->createSheet();
        $sheet1->getCell('A1')->setValue(10);
        $sheet1->getCell('A2')->setValue(20);
        $sheet1->getCell('A3')->setValue(30);
        $sheet1->getCell('A4')->setValue(40);
        $sheet1->getCell('A5')->setValue(50);
        $sheet1->setTitle('OtherSheet');
        $this->spreadsheet->addNamedRange(new NamedRange('newnr', $sheet1, '$A$2:$A$4'));

        $this->setCell('B1', $cellReference);
        $this->setCell('B2', $a1);
        if ($cellReference === 'omitted') {
            $sheet->getCell('B3')->setValue('=SUM(INDIRECT())');
        } elseif ($a1 === 'omitted') {
            $sheet->getCell('B3')->setValue('=SUM(INDIRECT(B1))');
        } else {
            $sheet->getCell('B3')->setValue('=SUM(INDIRECT(B1, B2))');
        }

        $result = $sheet->getCell('B3')->getCalculatedValue();
        self::assertSame($expectedResult, $result);
    }

    public function providerINDIRECT()
    {
        return require 'tests/data/Calculation/LookupRef/INDIRECT.php';
    }

    public function testINDIRECTLocalDefinedName(): void
    {
        $sheet = $this->sheet;

        $sheet1 = $this->spreadsheet->createSheet();
        $sheet1->getCell('A1')->setValue(10);
        $sheet1->getCell('A2')->setValue(20);
        $sheet1->getCell('A3')->setValue(30);
        $sheet1->getCell('A4')->setValue(40);
        $sheet1->getCell('A5')->setValue(50);
        $sheet1->setTitle('OtherSheet');
        $this->spreadsheet->addNamedRange(new NamedRange('newnr', $sheet1, '$A$2:$A$4', true)); // defined locally, only usable on sheet1

        $sheet1->getCell('B1')->setValue('newnr');
        $sheet1->getCell('B3')->setValue('=SUM(INDIRECT(B1))');
        $result = $sheet1->getCell('B3')->getCalculatedValue();
        self::assertSame(90, $result);

        $sheet->getCell('B1')->setValue('newnr');
        $sheet->getCell('B3')->setValue('=SUM(INDIRECT(B1))');
        $result = $sheet->getCell('B3')->getCalculatedValue();
        self::assertSame('#REF!', $result);
    }
}
