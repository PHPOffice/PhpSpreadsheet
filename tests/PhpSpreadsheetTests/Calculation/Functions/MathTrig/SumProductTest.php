<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;

class SumProductTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerSUMPRODUCT
     */
    public function testSUMPRODUCT(mixed $expectedResult, mixed ...$args): void
    {
        $sheet = $this->getSheet();
        $row = 0;
        $arrayArg = '';
        foreach ($args as $arr) {
            $arr2 = Functions::flattenArray($arr);
            $startRow = 0;
            foreach ($arr2 as $arr3) {
                ++$row;
                if (!$startRow) {
                    $startRow = $row;
                }
                $sheet->getCell("A$row")->setValue($arr3);
            }
            $arrayArg .= "A$startRow:A$row,";
        }
        $arrayArg = substr($arrayArg, 0, -1); // strip trailing comma
        $sheet->getCell('B1')->setValue("=SUMPRODUCT($arrayArg)");
        $result = $sheet->getCell('B1')->getCalculatedValue();
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public static function providerSUMPRODUCT(): array
    {
        return require 'tests/data/Calculation/MathTrig/SUMPRODUCT.php';
    }

    public function testBoolsAsInt(): void
    {
        // issue 3389 not handling unary minus with boolean value
        $sheet = $this->getSheet();
        $sheet->fromArray(
            [
                ['Range 1', 'Range 2', null, 'Blue matches', 'Red matches'],
                [0, 'Red', null, '=SUMPRODUCT(--(B3:B10=1), --(C3:C10="BLUE"))', '=SUMPRODUCT(--(B3:B10=1), --(C3:C10="RED"))'],
                [1, 'Blue'],
                [0, 'Blue'],
                [1, 'Red'],
                [1, 'Blue'],
                [0, 'Blue'],
                [1, 'Red'],
                [1, 'Blue'],
            ],
            null, // null value
            'B2', // start cell
            true // strict null comparison
        );
        self::assertSame(3, $sheet->getCell('E3')->getCalculatedValue());
        self::assertSame(2, $sheet->getCell('F3')->getCalculatedValue());
    }
}
