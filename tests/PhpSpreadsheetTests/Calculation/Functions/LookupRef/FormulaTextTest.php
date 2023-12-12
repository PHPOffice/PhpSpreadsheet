<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\LookupRef;

use PhpOffice\PhpSpreadsheet\Calculation\LookupRef\Formula;

/**
 * Class FormulaTextTest.
 */
class FormulaTextTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerFormulaText
     */
    public function testFormulaText(string $expectedResult, mixed $value): void
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

    public static function providerFormulaText(): array
    {
        return require 'tests/data/Calculation/LookupRef/FORMULATEXT.php';
    }

    public function testNoCell(): void
    {
        self::assertSame('#REF!', Formula::text('B5'));
    }
}
