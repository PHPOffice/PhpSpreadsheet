<?php

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xls;

use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PhpOffice\PhpSpreadsheet\Style\Conditional;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PHPUnit\Framework\TestCase;

class ConditionalFormattingExpressionTest extends TestCase
{
    /**
     * @var Worksheet
     */
    protected $sheet;

    protected function setUp(): void
    {
        $filename = 'tests/data/Reader/XLS/CF_Expression_Comparisons.xls';
        $reader = new Xls();
        $spreadsheet = $reader->load($filename);
        $this->sheet = $spreadsheet->getActiveSheet();
    }

    /**
     * @dataProvider conditionalFormattingProvider
     */
    public function testReadConditionalFormatting(string $expectedRange, array $expectedRule): void
    {
        $hasConditionalStyles = $this->sheet->conditionalStylesExists($expectedRange);
        self::assertTrue($hasConditionalStyles);

        $conditionalStyles = $this->sheet->getConditionalStyles($expectedRange);

        foreach ($conditionalStyles as $index => $conditionalStyle) {
            self::assertSame($expectedRule[$index]['type'], $conditionalStyle->getConditionType());
            self::assertSame($expectedRule[$index]['operator'], $conditionalStyle->getOperatorType());
            self::assertSame($expectedRule[$index]['conditions'], $conditionalStyle->getConditions());
        }
    }

    public function conditionalFormattingProvider(): array
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
