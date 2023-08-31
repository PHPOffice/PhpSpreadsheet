<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\Operands\StructuredReference;
use PhpOffice\PhpSpreadsheet\NamedRange;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

class ParseFormulaTest extends TestCase
{
    /**
     * @dataProvider providerBinaryOperations
     */
    public function testParseOperations(array $expectedStack, string $formula): void
    {
        $spreadsheet = new Spreadsheet();
        $spreadsheet->addNamedRange(new NamedRange('GROUP1', $spreadsheet->getActiveSheet(), 'B2:D4'));
        $spreadsheet->addNamedRange(new NamedRange('GROUP2', $spreadsheet->getActiveSheet(), 'D4:F6'));

        $parser = Calculation::getInstance($spreadsheet);
        $stack = $parser->parseFormula($formula);
        self::assertEquals($expectedStack, $stack);
    }

    public static function providerBinaryOperations(): array
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
            'Integer Numbers with Operator' => [
                [
                    ['type' => 'Value', 'value' => 2, 'reference' => null],
                    ['type' => 'Value', 'value' => 3, 'reference' => null],
                    ['type' => 'Binary Operator', 'value' => '*', 'reference' => null],
                ],
                '=2*3',
            ],
            'Float Numbers with Operator' => [
                [
                    ['type' => 'Value', 'value' => 2.5, 'reference' => null],
                    ['type' => 'Value', 'value' => 3.5, 'reference' => null],
                    ['type' => 'Binary Operator', 'value' => '*', 'reference' => null],
                ],
                '=2.5*3.5',
            ],
            'Strings with Operator' => [
                [
                    ['type' => 'Value', 'value' => '"HELLO"', 'reference' => null],
                    ['type' => 'Value', 'value' => '"WORLD"', 'reference' => null],
                    ['type' => 'Binary Operator', 'value' => '&', 'reference' => null],
                ],
                '="HELLO"&"WORLD"',
            ],
            'Error' => [
                [
                    ['type' => 'Value', 'value' => '#DIV0!', 'reference' => null],
                ],
                '=#DIV0!',
            ],
            'Cell Range' => [
                [
                    ['type' => 'Cell Reference', 'value' => 'A1', 'reference' => 'A1'],
                    ['type' => 'Cell Reference', 'value' => 'C3', 'reference' => 'C3'],
                    ['type' => 'Binary Operator', 'value' => ':', 'reference' => null],
                ],
                '=A1:C3',
            ],
            'Chained Cell Range' => [
                [
                    ['type' => 'Cell Reference', 'value' => 'A1', 'reference' => 'A1'],
                    ['type' => 'Cell Reference', 'value' => 'C3', 'reference' => 'C3'],
                    ['type' => 'Binary Operator', 'value' => ':', 'reference' => null],
                    ['type' => 'Cell Reference', 'value' => 'E5', 'reference' => 'E5'],
                    ['type' => 'Binary Operator', 'value' => ':', 'reference' => null],
                ],
                '=A1:C3:E5',
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
            'Row Range' => [
                [
                    ['type' => 'Row Reference', 'value' => 'A2', 'reference' => 'A2'],
                    ['type' => 'Row Reference', 'value' => 'XFD3', 'reference' => 'XFD3'],
                    ['type' => 'Binary Operator', 'value' => ':', 'reference' => null],
                ],
                '=2:3',
            ],
            'Column Range' => [
                [
                    ['type' => 'Column Reference', 'value' => 'B1', 'reference' => 'B1'],
                    ['type' => 'Column Reference', 'value' => 'C1048576', 'reference' => 'C1048576'],
                    ['type' => 'Binary Operator', 'value' => ':', 'reference' => null],
                ],
                '=B:C',
            ],
            'Combined Cell Reference and Column Range' => [
                [
                    ['type' => 'Column Reference', 'value' => "'sheet1'!A1", 'reference' => "'sheet1'!A1"],
                    ['type' => 'Column Reference', 'value' => "'sheet1'!A1048576", 'reference' => "'sheet1'!A1048576"],
                    ['type' => 'Binary Operator', 'value' => ':', 'reference' => null],
                    ['type' => 'Operand Count for Function MIN()', 'value' => 1, 'reference' => null],
                    ['type' => 'Function', 'value' => 'MIN(', 'reference' => null],
                    ['type' => 'Cell Reference', 'value' => "'sheet1'!A1", 'reference' => "'sheet1'!A1"],
                    ['type' => 'Binary Operator', 'value' => '+', 'reference' => null],
                ],
                "=MIN('sheet1'!A:A) + 'sheet1'!A1",
            ],
            'Combined Cell Reference and Column Range with quote' => [
                [
                    ['type' => 'Column Reference', 'value' => "'Mark's sheet1'!A1", 'reference' => "'Mark's sheet1'!A1"],
                    ['type' => 'Column Reference', 'value' => "'Mark's sheet1'!A1048576", 'reference' => "'Mark's sheet1'!A1048576"],
                    ['type' => 'Binary Operator', 'value' => ':', 'reference' => null],
                    ['type' => 'Operand Count for Function MIN()', 'value' => 1, 'reference' => null],
                    ['type' => 'Function', 'value' => 'MIN(', 'reference' => null],
                    ['type' => 'Cell Reference', 'value' => "'Mark's sheet1'!A1", 'reference' => "'Mark's sheet1'!A1"],
                    ['type' => 'Binary Operator', 'value' => '+', 'reference' => null],
                ],
                "=MIN('Mark''s sheet1'!A:A) + 'Mark''s sheet1'!A1",
            ],
            'Combined Cell Reference and Column Range with unescaped quote' => [
                [
                    ['type' => 'Column Reference', 'value' => "'Mark's sheet1'!A1", 'reference' => "'Mark's sheet1'!A1"],
                    ['type' => 'Column Reference', 'value' => "'Mark's sheet1'!A1048576", 'reference' => "'Mark's sheet1'!A1048576"],
                    ['type' => 'Binary Operator', 'value' => ':', 'reference' => null],
                    ['type' => 'Operand Count for Function MIN()', 'value' => 1, 'reference' => null],
                    ['type' => 'Function', 'value' => 'MIN(', 'reference' => null],
                    ['type' => 'Cell Reference', 'value' => "'Mark's sheet1'!A1", 'reference' => "'Mark's sheet1'!A1"],
                    ['type' => 'Binary Operator', 'value' => '+', 'reference' => null],
                ],
                "=MIN('Mark's sheet1'!A:A) + 'Mark's sheet1'!A1",
            ],
            'Combined Column Range and Cell Reference' => [
                [
                    ['type' => 'Cell Reference', 'value' => "'sheet1'!A1", 'reference' => "'sheet1'!A1"],
                    ['type' => 'Column Reference', 'value' => "'sheet1'!A1", 'reference' => "'sheet1'!A1"],
                    ['type' => 'Column Reference', 'value' => "'sheet1'!A1048576", 'reference' => "'sheet1'!A1048576"],
                    ['type' => 'Binary Operator', 'value' => ':', 'reference' => null],
                    ['type' => 'Operand Count for Function MIN()', 'value' => 1, 'reference' => null],
                    ['type' => 'Function', 'value' => 'MIN(', 'reference' => null],
                    ['type' => 'Binary Operator', 'value' => '+', 'reference' => null],
                ],
                "='sheet1'!A1 + MIN('sheet1'!A:A)",
            ],
            'Combined Column Range and Cell Reference with quote' => [
                [
                    ['type' => 'Cell Reference', 'value' => "'Mark's sheet1'!A1", 'reference' => "'Mark's sheet1'!A1"],
                    ['type' => 'Column Reference', 'value' => "'Mark's sheet1'!A1", 'reference' => "'Mark's sheet1'!A1"],
                    ['type' => 'Column Reference', 'value' => "'Mark's sheet1'!A1048576", 'reference' => "'Mark's sheet1'!A1048576"],
                    ['type' => 'Binary Operator', 'value' => ':', 'reference' => null],
                    ['type' => 'Operand Count for Function MIN()', 'value' => 1, 'reference' => null],
                    ['type' => 'Function', 'value' => 'MIN(', 'reference' => null],
                    ['type' => 'Binary Operator', 'value' => '+', 'reference' => null],
                ],
                "='Mark''s sheet1'!A1 + MIN('Mark''s sheet1'!A:A)",
            ],
            'Combined Column Range and Cell Reference with unescaped quote' => [
                [
                    ['type' => 'Cell Reference', 'value' => "'Mark's sheet1'!A1", 'reference' => "'Mark's sheet1'!A1"],
                    ['type' => 'Column Reference', 'value' => "'Mark's sheet1'!A1", 'reference' => "'Mark's sheet1'!A1"],
                    ['type' => 'Column Reference', 'value' => "'Mark's sheet1'!A1048576", 'reference' => "'Mark's sheet1'!A1048576"],
                    ['type' => 'Binary Operator', 'value' => ':', 'reference' => null],
                    ['type' => 'Operand Count for Function MIN()', 'value' => 1, 'reference' => null],
                    ['type' => 'Function', 'value' => 'MIN(', 'reference' => null],
                    ['type' => 'Binary Operator', 'value' => '+', 'reference' => null],
                ],
                "='Mark's sheet1'!A1 + MIN('Mark's sheet1'!A:A)",
            ],
            'Range with Defined Names' => [
                [
                    ['type' => 'Defined Name', 'value' => 'GROUP1', 'reference' => 'GROUP1'],
                    ['type' => 'Defined Name', 'value' => 'D4', 'reference' => 'GROUP2'],
                    ['type' => 'Binary Operator', 'value' => ':', 'reference' => null],
                    ['type' => 'Defined Name', 'value' => 'F6', 'reference' => 'GROUP2'],
                    ['type' => 'Binary Operator', 'value' => ':', 'reference' => null],
                ],
                '=GROUP1:GROUP2',
            ],
            'Named Range with Binary Operator' => [
                [
                    ['type' => 'Defined Name', 'value' => 'DEFINED_NAME_1', 'reference' => 'DEFINED_NAME_1'],
                    ['type' => 'Defined Name', 'value' => 'DEFINED_NAME_2', 'reference' => 'DEFINED_NAME_2'],
                    ['type' => 'Binary Operator', 'value' => '/', 'reference' => null],
                ],
                '=DEFINED_NAME_1/DEFINED_NAME_2',
            ],
            'Named Range Intersection' => [
                [
                    ['type' => 'Defined Name', 'value' => 'DEFINED_NAME_1', 'reference' => 'DEFINED_NAME_1'],
                    ['type' => 'Defined Name', 'value' => 'DEFINED_NAME_2', 'reference' => 'DEFINED_NAME_2'],
                    ['type' => 'Binary Operator', 'value' => '∩', 'reference' => null],
                ],
                '=DEFINED_NAME_1 DEFINED_NAME_2',
            ],
            'Fully Qualified Structured Reference' => [
                [
                    ['type' => 'Structured Reference', 'value' => new StructuredReference('DeptSales[@Commission Amount]'), 'reference' => null],
                ],
                '=DeptSales[@Commission Amount]',
            ],
            'Fully Qualified Nested Structured Reference' => [
                [
                    ['type' => 'Structured Reference', 'value' => new StructuredReference('DeptSales[[#Totals],[Sales Amount]]'), 'reference' => null],
                ],
                '=DeptSales[[#Totals],[Sales Amount]]',
            ],
            'Complex Range Fully Qualified Nested Structured Reference' => [
                [
                    ['type' => 'Structured Reference', 'value' => new StructuredReference('Sales_Data[[#This Row],[Q1]:[Q4]]'), 'reference' => null],
                ],
                '=Sales_Data[[#This Row],[Q1]:[Q4]]',
            ],
            'Complex Range Fully Qualified Nested Structured Reference 2' => [
                [
                    ['type' => 'Structured Reference', 'value' => new StructuredReference('DeptSales[[#Headers],[Region]:[Commission Amount]]'), 'reference' => null],
                ],
                '=DeptSales[[#Headers],[Region]:[Commission Amount]]',
            ],
            [
                'Multi-RowGroup Fully Qualified Nested Structured Reference' => [
                    ['type' => 'Structured Reference', 'value' => new StructuredReference('DeptSales[[#Headers],[#Data],[% Commission]]'), 'reference' => null],
                ],
                '=DeptSales[[#Headers],[#Data],[% Commission]]',
            ],
            'Unqualified Structured Reference' => [
                [
                    ['type' => 'Structured Reference', 'value' => new StructuredReference('[@Quantity]'), 'reference' => null],
                ],
                '=[@Quantity]',
            ],
            'Unqualified Nested Structured Reference' => [
                [
                    ['type' => 'Structured Reference', 'value' => new StructuredReference('[@[Unit Price]]'), 'reference' => null],
                ],
                '=[@[Unit Price]]',
            ],
            'Fully Qualified Full Table Structured Reference' => [
                [
                    ['type' => 'Structured Reference', 'value' => new StructuredReference('DeptSales[]'), 'reference' => null],
                ],
                '=DeptSales[]',
            ],
            'Unqualified Full Table Structured Reference' => [
                [
                    ['type' => 'Structured Reference', 'value' => new StructuredReference('[]'), 'reference' => null],
                ],
                '=[]',
            ],
            'Structured Reference Arithmetic' => [
                [
                    ['type' => 'Structured Reference', 'value' => new StructuredReference('[@Quantity]'), 'reference' => null],
                    ['type' => 'Structured Reference', 'value' => new StructuredReference('[@[Unit Price]]'), 'reference' => null],
                    ['type' => 'Binary Operator', 'value' => '*', 'reference' => null],
                ],
                '=[@Quantity]*[@[Unit Price]]',
            ],
            'Structured Reference Intersection' => [
                [
                    ['type' => 'Structured Reference', 'value' => new StructuredReference('DeptSales[[Sales Person]:[Sales Amount]]'), 'reference' => null],
                    ['type' => 'Structured Reference', 'value' => new StructuredReference('DeptSales[[Region]:[% Commission]]'), 'reference' => null],
                    ['type' => 'Binary Operator', 'value' => '∩', 'reference' => null],
                ],
                '=DeptSales[[Sales Person]:[Sales Amount]] DeptSales[[Region]:[% Commission]]',
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
