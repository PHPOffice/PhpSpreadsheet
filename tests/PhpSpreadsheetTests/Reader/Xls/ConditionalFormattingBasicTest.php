<?php

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xls;

use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Conditional;
use PHPUnit\Framework\TestCase;

class ConditionalFormattingBasicTest extends TestCase
{
    /**
     * @dataProvider conditionalFormattingProvider
     */
    public function testReadConditionalFormatting(string $expectedRange, array $expectedRules): void
    {
        $filename = 'tests/data/Reader/XLS/CF_Basic_Comparisons.xls';
        $reader = new Xls();
        $spreadsheet = $reader->load($filename);
        $sheet = $spreadsheet->getActiveSheet();
        $hasConditionalStyles = $sheet->conditionalStylesExists($expectedRange);
        self::assertTrue($hasConditionalStyles);

        $conditionalStyles = $sheet->getConditionalStyles($expectedRange);

        foreach ($conditionalStyles as $index => $conditionalStyle) {
            self::assertSame($expectedRules[$index]['type'], $conditionalStyle->getConditionType());
            self::assertSame($expectedRules[$index]['operator'], $conditionalStyle->getOperatorType());
            self::assertSame($expectedRules[$index]['conditions'], $conditionalStyle->getConditions());
        }
        $spreadsheet->disconnectWorksheets();
    }

    public static function conditionalFormattingProvider(): array
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

    public function testReadConditionalFormattingStyles(): void
    {
        $filename = 'tests/data/Reader/XLS/CF_Basic_Comparisons.xls';
        $reader = new Xls();
        $spreadsheet = $reader->load($filename);
        $sheet = $spreadsheet->getActiveSheet();
        $expectedRange = 'A2:E5';
        $hasConditionalStyles = $sheet->conditionalStylesExists($expectedRange);
        self::assertTrue($hasConditionalStyles);

        $conditionalStyles = $sheet->getConditionalStyles($expectedRange);
        self::assertCount(3, $conditionalStyles);

        $style = $conditionalStyles[0]->getStyle();
        $font = $style->getFont();
        self::assertSame('FF0000FF', $font->getColor()->getArgb());
        self::assertNull($font->getItalic());
        self::assertNull($font->getStrikethrough());
        // Fill not handled correctly - forget for now
        $borders = $style->getBorders();
        self::assertSame(Border::BORDER_OMIT, $borders->getLeft()->getBorderStyle());
        self::assertSame(Border::BORDER_OMIT, $borders->getRight()->getBorderStyle());
        self::assertSame(Border::BORDER_OMIT, $borders->getTop()->getBorderStyle());
        self::assertSame(Border::BORDER_OMIT, $borders->getBottom()->getBorderStyle());
        self::assertNull($style->getNumberFormat()->getFormatCode());

        $style = $conditionalStyles[1]->getStyle();
        $font = $style->getFont();
        self::assertSame('FF800000', $font->getColor()->getArgb());
        self::assertNull($font->getItalic());
        self::assertNull($font->getStrikethrough());
        // Fill not handled correctly - forget for now
        $borders = $style->getBorders();
        self::assertSame(Border::BORDER_OMIT, $borders->getLeft()->getBorderStyle());
        self::assertSame(Border::BORDER_OMIT, $borders->getRight()->getBorderStyle());
        self::assertSame(Border::BORDER_OMIT, $borders->getTop()->getBorderStyle());
        self::assertSame(Border::BORDER_OMIT, $borders->getBottom()->getBorderStyle());
        self::assertNull($style->getNumberFormat()->getFormatCode());

        $style = $conditionalStyles[2]->getStyle();
        $font = $style->getFont();
        self::assertSame('FF00FF00', $font->getColor()->getArgb());
        self::assertNull($font->getItalic());
        self::assertNull($font->getStrikethrough());
        // Fill not handled correctly - forget for now
        $borders = $style->getBorders();
        self::assertSame(Border::BORDER_OMIT, $borders->getLeft()->getBorderStyle());
        self::assertSame(Border::BORDER_OMIT, $borders->getRight()->getBorderStyle());
        self::assertSame(Border::BORDER_OMIT, $borders->getTop()->getBorderStyle());
        self::assertSame(Border::BORDER_OMIT, $borders->getBottom()->getBorderStyle());
        self::assertNull($style->getNumberFormat()->getFormatCode());

        $spreadsheet->disconnectWorksheets();
    }
}
