<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\LookupRef;

use PhpOffice\PhpSpreadsheet\Calculation\LookupRef\Formula;

/**
 * Class FormulaTextTest.
 */
class FormulaTextTest extends AllSetupTeardown
{
    /**
     * @param mixed $value
     * @dataProvider providerFormulaText
     */
    public function testFormulaText(string $expectedResult, $value): void
    {
        $sheet = $this->getSheet();
        $reference = 'A1';
        if ($value !== null) {
            $sheet->getCell($reference)->setValue($value);
        }
        $sheet->getCell('D1')->setValue("=FORMULATEXT($reference)");
        $result = $sheet->getCell('D1')->getCalculatedValue();
        self::assertSame($expectedResult, $result);
    }

    public function providerFormulaText(): array
    {
        return require 'tests/data/Calculation/LookupRef/FORMULATEXT.php';
    }

    public function testNoCell(): void
    {
        self::assertSame('#REF!', Formula::text('B5'));
    }

    public function testArrayFormula(): void
    {
        $sheet = $this->getSheet();
        $sheet->setCellValue('B2', '=SEQUENCE(3,3)', true, 'B2:D4');

        // Execute the calculation to populate the spillage cells
        $sheet->getCell('B2')->getCalculatedValue(true);
        $sheet->setCellValue('A1', '=FORMULATEXT(B2)');
        $sheet->setCellValue('E5', '=FORMULATEXT(D4)');

        $result1 = $sheet->getCell('A1')->getCalculatedValue();
        self::assertSame('{=SEQUENCE(3,3)}', $result1, 'Formula Cell');

        $result2 = $sheet->getCell('E5')->getCalculatedValue();
        self::assertSame('{=SEQUENCE(3,3)}', $result2, 'Spill Range Cell');
    }
}
