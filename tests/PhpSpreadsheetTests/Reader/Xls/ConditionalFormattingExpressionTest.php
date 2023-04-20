<?php

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xls;

use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PhpOffice\PhpSpreadsheet\Style\Conditional;
use PHPUnit\Framework\TestCase;

class ConditionalFormattingExpressionTest extends TestCase
{
    /**
     * @dataProvider conditionalFormattingProvider
     */
    public function testReadConditionalFormatting(string $expectedRange, array $expectedRule): void
    {
        $filename = 'tests/data/Reader/XLS/CF_Expression_Comparisons.xls';
        $reader = new Xls();
        $spreadsheet = $reader->load($filename);
        $sheet = $spreadsheet->getActiveSheet();
        $hasConditionalStyles = $sheet->conditionalStylesExists($expectedRange);
        self::assertTrue($hasConditionalStyles);

        $conditionalStyles = $sheet->getConditionalStyles($expectedRange);

        foreach ($conditionalStyles as $index => $conditionalStyle) {
            self::assertSame($expectedRule[$index]['type'], $conditionalStyle->getConditionType());
            self::assertSame($expectedRule[$index]['operator'], $conditionalStyle->getOperatorType());
            self::assertSame($expectedRule[$index]['conditions'], $conditionalStyle->getConditions());
        }
        $spreadsheet->disconnectWorksheets();
    }

    public static function conditionalFormattingProvider(): array
    {
        return [
            [
                'A3:D8',
                [
                    [
                        'type' => Conditional::CONDITION_EXPRESSION,
                        'operator' => Conditional::OPERATOR_NONE,
                        'conditions' => [
                            '$C1="USA"',
                        ],
                    ],
                ],
            ],
            [
                'A13:D18',
                [
                    [
                        'type' => Conditional::CONDITION_EXPRESSION,
                        'operator' => Conditional::OPERATOR_NONE,
                        'conditions' => [
                            'AND($C1="USA",$D1="Q4")',
                        ],
                    ],
                ],
            ],
        ];
    }
}
