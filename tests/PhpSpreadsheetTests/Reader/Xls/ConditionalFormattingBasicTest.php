<?php

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xls;

use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PhpOffice\PhpSpreadsheet\Style\Conditional;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PHPUnit\Framework\TestCase;

class ConditionalFormattingBasicTest extends TestCase
{
    /**
     * @var Worksheet
     */
    protected $sheet;

    protected function setUp(): void
    {
        $filename = 'tests/data/Reader/XLS/CF_Basic_Comparisons.xls';
        $reader = new Xls();
        $spreadsheet = $reader->load($filename);
        $this->sheet = $spreadsheet->getActiveSheet();
    }

    /**
     * @dataProvider conditionalFormattingProvider
     */
    public function testReadConditionalFormatting(string $expectedRange, array $expectedRules): void
    {
        $hasConditionalStyles = $this->sheet->conditionalStylesExists($expectedRange);
        self::assertTrue($hasConditionalStyles);

        $conditionalStyles = $this->sheet->getConditionalStyles($expectedRange);

        foreach ($conditionalStyles as $index => $conditionalStyle) {
            self::assertSame($expectedRules[$index]['type'], $conditionalStyle->getConditionType());
            self::assertSame($expectedRules[$index]['operator'], $conditionalStyle->getOperatorType());
            self::assertSame($expectedRules[$index]['conditions'], $conditionalStyle->getConditions());
        }
    }

    public function conditionalFormattingProvider(): array
    {
        return [
            [
                'A2:E5',
                [
                    [
                        'type' => Conditional::CONDITION_CELLIS,
                        'operator' => Conditional::OPERATOR_EQUAL,
                        'conditions' => [
                            0,
                        ],
                    ],
                    [
                        'type' => Conditional::CONDITION_CELLIS,
                        'operator' => Conditional::OPERATOR_GREATERTHAN,
                        'conditions' => [
                            0,
                        ],
                    ],
                    [
                        'type' => Conditional::CONDITION_CELLIS,
                        'operator' => Conditional::OPERATOR_LESSTHAN,
                        'conditions' => [
                            0,
                        ],
                    ],
                ],
            ],
            [
                'A10:E13',
                [
                    [
                        'type' => Conditional::CONDITION_CELLIS,
                        'operator' => Conditional::OPERATOR_EQUAL,
                        'conditions' => [
                            '$H$9',
                        ],
                    ],
                    [
                        'type' => Conditional::CONDITION_CELLIS,
                        'operator' => Conditional::OPERATOR_GREATERTHAN,
                        'conditions' => [
                            '$H$9',
                        ],
                    ],
                    [
                        'type' => Conditional::CONDITION_CELLIS,
                        'operator' => Conditional::OPERATOR_LESSTHAN,
                        'conditions' => [
                            '$H$9',
                        ],
                    ],
                ],
            ],
            [
                'A18:A20',
                [
                    [
                        'type' => Conditional::CONDITION_CELLIS,
                        'operator' => Conditional::OPERATOR_BETWEEN,
                        'conditions' => [
                            '$B1',
                            '$C1',
                        ],
                    ],
                ],
            ],
            [
                'A24:E27',
                [
                    [
                        'type' => Conditional::CONDITION_CELLIS,
                        'operator' => Conditional::OPERATOR_BETWEEN,
                        'conditions' => [
                            'AVERAGE($A$24:$E$27)-STDEV($A$24:$E$27)',
                            'AVERAGE($A$24:$E$27)+STDEV($A$24:$E$27)',
                        ],
                    ],
                    [
                        'type' => Conditional::CONDITION_CELLIS,
                        'operator' => Conditional::OPERATOR_GREATERTHAN,
                        'conditions' => [
                            'AVERAGE($A$24:$E$27)+STDEV($A$24:$E$27)',
                        ],
                    ],
                    [
                        'type' => Conditional::CONDITION_CELLIS,
                        'operator' => Conditional::OPERATOR_LESSTHAN,
                        'conditions' => [
                            'AVERAGE($A$24:$E$27)-STDEV($A$24:$E$27)',
                        ],
                    ],
                ],
            ],
            [
                'A31:A33',
                [
                    [
                        'type' => Conditional::CONDITION_CELLIS,
                        'operator' => Conditional::OPERATOR_EQUAL,
                        'conditions' => [
                            '"LOVE"',
                        ],
                    ],
                    [
                        'type' => Conditional::CONDITION_CELLIS,
                        'operator' => Conditional::OPERATOR_EQUAL,
                        'conditions' => [
                            '"PHP"',
                        ],
                    ],
                ],
            ],
        ];
    }
}
