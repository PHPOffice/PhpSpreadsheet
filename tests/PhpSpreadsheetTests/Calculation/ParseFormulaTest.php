<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PHPUnit\Framework\TestCase;

class ParseFormulaTest extends TestCase
{
    /**
     * @dataProvider providerBinaryOperations
     */
    public function testParseOperations(array $expectedStack, string $formula): void
    {
        $parser = Calculation::getInstance();
        $stack = $parser->parseFormula($formula);
        self::assertSame($expectedStack, $stack);
    }

    public function providerBinaryOperations(): array
    {
        return [
            'Unary negative with Value' => [
                [
                    ['type' => 'Value', 'value' => 3, 'reference' => null],
                    ['type' => 'Unary Operator', 'value' => '~', 'reference' => null],
                ],
                '=-3',
            ],
            'Unary negative percentage with Value' => [
                [
                    ['type' => 'Value', 'value' => 3, 'reference' => null],
                    ['type' => 'Unary Operator', 'value' => '%', 'reference' => null],
                    ['type' => 'Unary Operator', 'value' => '~', 'reference' => null],
                ],
                '=-3%',
            ],
            'Binary minus with Values' => [
                [
                    ['type' => 'Value', 'value' => 3, 'reference' => null],
                    ['type' => 'Value', 'value' => 4, 'reference' => null],
                    ['type' => 'Binary Operator', 'value' => '-', 'reference' => null],
                ],
                '=3-4',
            ],
            'Unary negative with Cell Reference' => [
                [
                    ['type' => 'Cell Reference', 'value' => 'A1', 'reference' => 'A1'],
                    ['type' => 'Unary Operator', 'value' => '~', 'reference' => null],
                ],
                '=-A1',
            ],
            'Unary negative with FQ Cell Reference' => [
                [
                    ['type' => 'Cell Reference', 'value' => "'Sheet 1'!A1", 'reference' => "'Sheet 1'!A1"],
                    ['type' => 'Unary Operator', 'value' => '~', 'reference' => null],
                ],
                "=-'Sheet 1'!A1",
            ],
            'Unary negative percentage with Cell Reference' => [
                [
                    ['type' => 'Cell Reference', 'value' => 'A1', 'reference' => 'A1'],
                    ['type' => 'Unary Operator', 'value' => '%', 'reference' => null],
                    ['type' => 'Unary Operator', 'value' => '~', 'reference' => null],
                ],
                '=-A1%',
            ],
            'Unary negative with Defined Name' => [
                [
                    ['type' => 'Defined Name', 'value' => 'DEFINED_NAME', 'reference' => 'DEFINED_NAME'],
                    ['type' => 'Unary Operator', 'value' => '~', 'reference' => null],
                ],
                '=-DEFINED_NAME',
            ],
            'Unary negative percentage with Defined Name' => [
                [
                    ['type' => 'Defined Name', 'value' => 'DEFINED_NAME', 'reference' => 'DEFINED_NAME'],
                    ['type' => 'Unary Operator', 'value' => '%', 'reference' => null],
                    ['type' => 'Unary Operator', 'value' => '~', 'reference' => null],
                ],
                '=-DEFINED_NAME%',
            ],
            'Cell Range' => [
                [
                    ['type' => 'Cell Reference', 'value' => 'A1', 'reference' => 'A1'],
                    ['type' => 'Cell Reference', 'value' => 'C3', 'reference' => 'C3'],
                    ['type' => 'Binary Operator', 'value' => ':', 'reference' => null],
                ],
                '=A1:C3',
            ],
            'Cell Range Intersection' => [
                [
                    ['type' => 'Cell Reference', 'value' => 'A1', 'reference' => 'A1'],
                    ['type' => 'Cell Reference', 'value' => 'C3', 'reference' => 'C3'],
                    ['type' => 'Binary Operator', 'value' => ':', 'reference' => null],
                    ['type' => 'Cell Reference', 'value' => 'B2', 'reference' => 'B2'],
                    ['type' => 'Cell Reference', 'value' => 'D4', 'reference' => 'D4'],
                    ['type' => 'Binary Operator', 'value' => ':', 'reference' => null],
                    ['type' => 'Binary Operator', 'value' => '∩', 'reference' => null],
                ],
                '=A1:C3 B2:D4',
            ],
            'Named Range Intersection' => [
                [
                    ['type' => 'Defined Name', 'value' => 'DEFINED_NAME_1', 'reference' => 'DEFINED_NAME_1'],
                    ['type' => 'Defined Name', 'value' => 'DEFINED_NAME_2', 'reference' => 'DEFINED_NAME_2'],
                    ['type' => 'Binary Operator', 'value' => '∩', 'reference' => null],
                ],
                '=DEFINED_NAME_1 DEFINED_NAME_2',
            ],
            //            'Cell Range Union' => [
            //                [
            //                    ['type' => 'Cell Reference', 'value' => 'A1', 'reference' => 'A1'],
            //                    ['type' => 'Cell Reference', 'value' => 'C3', 'reference' => 'C3'],
            //                    ['type' => 'Binary Operator', 'value' => ':', 'reference' => null],
            //                    ['type' => 'Cell Reference', 'value' => 'B2', 'reference' => 'B2'],
            //                    ['type' => 'Cell Reference', 'value' => 'D4', 'reference' => 'D4'],
            //                    ['type' => 'Binary Operator', 'value' => ':', 'reference' => null],
            //                    ['type' => 'Binary Operator', 'value' => '∪', 'reference' => null],
            //                ],
            //                '=A1:C3,B2:D4',
            //            ],
            //            'Named Range Union' => [
            //                [
            //                    ['type' => 'Defined Name', 'value' => 'DEFINED_NAME_1', 'reference' => 'DEFINED_NAME_1'],
            //                    ['type' => 'Defined Name', 'value' => 'DEFINED_NAME_2', 'reference' => 'DEFINED_NAME_2'],
            //                    ['type' => 'Binary Operator', 'value' => '∪', 'reference' => null],
            //                ],
            //                '=DEFINED_NAME_1,DEFINED_NAME_2',
            //            ],
        ];
    }
}
