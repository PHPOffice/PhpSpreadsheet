<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

class ProductTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerPRODUCT
     *
     * @param mixed $expectedResult
     */
    public function testPRODUCT($expectedResult, ...$args): void
    {
        $sheet = $this->getSheet();
        $row = 0;
        foreach ($args as $arg) {
            ++$row;
            $sheet->getCell("A$row")->setValue($arg);
        }
        $sheet->getCell('B1')->setValue("=PRODUCT(A1:A$row)");
        $result = $sheet->getCell('B1')->getCalculatedValue();
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public static function providerPRODUCT(): array
    {
        return require 'tests/data/Calculation/MathTrig/PRODUCT.php';
    }
}
