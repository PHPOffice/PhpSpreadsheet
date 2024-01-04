<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Worksheet;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Conditional;
use PHPUnit\Framework\TestCase;

class ConditionalStyleTest extends TestCase
{
    protected Spreadsheet $spreadsheet;

    protected function setUp(): void
    {
        parent::setUp();

        $this->spreadsheet = new Spreadsheet();

        $conditional1 = new Conditional();
        $conditional1->setConditionType(Conditional::CONDITION_CELLIS);
        $conditional1->setOperatorType(Conditional::OPERATOR_LESSTHAN);
        $conditional1->addCondition('0');
        $conditional1->getStyle()->getFont()->getColor()->setARGB(Color::COLOR_RED);
        $conditional1->getStyle()->getFont()->setBold(true);

        $conditional2 = new Conditional();
        $conditional2->setConditionType(Conditional::CONDITION_CELLIS);
        $conditional2->setOperatorType(Conditional::OPERATOR_EQUAL);
        $conditional2->addCondition('0');
        $conditional2->getStyle()->getFont()->getColor()->setARGB(Color::COLOR_YELLOW);
        $conditional2->getStyle()->getFont()->setBold(true);

        $conditional3 = new Conditional();
        $conditional3->setConditionType(Conditional::CONDITION_CELLIS);
        $conditional3->setOperatorType(Conditional::OPERATOR_GREATERTHAN);
        $conditional3->addCondition('0');
        $conditional3->getStyle()->getFont()->getColor()->setARGB(Color::COLOR_GREEN);
        $conditional3->getStyle()->getFont()->setBold(true);

        $conditionalStyles = $this->spreadsheet->getActiveSheet()->getStyle('A1:C3')->getConditionalStyles();
        $conditionalStyles[] = $conditional1;
        $conditionalStyles[] = $conditional2;
        $conditionalStyles[] = $conditional3;

        $this->spreadsheet->getActiveSheet()->getStyle('A1:C3')->setConditionalStyles($conditionalStyles);

        $this->spreadsheet->getActiveSheet()
            ->duplicateConditionalStyle(
                $this->spreadsheet->getActiveSheet()->getConditionalStyles('A1:C3'),
                'F1'
            );
    }

    /**
     * @dataProvider cellConditionalStylesProvider
     */
    public function testCellHasConditionalStyles(string $cellReference, bool $expectedHasConditionalStyles): void
    {
        $cellHasConditionalStyles = $this->spreadsheet->getActiveSheet()->conditionalStylesExists($cellReference);

        self::assertSame($expectedHasConditionalStyles, $cellHasConditionalStyles);
    }

    /**
     * @dataProvider cellConditionalStylesProvider
     */
    public function testCellGetConditionalStyles(string $cellReference, bool $expectedGetConditionalStyles): void
    {
        $cellHasConditionalStyles = $this->spreadsheet->getActiveSheet()->getConditionalStyles($cellReference);

        self::assertSame($expectedGetConditionalStyles, !empty($cellHasConditionalStyles));
    }

    public static function cellConditionalStylesProvider(): array
    {
        return [
            ['A1', true],
            ['B2', true],
            ['B4', false],
            ['A1:C3', true],
            ['A1:B2', false],
            ['F1', true],
            ['F2', false],
            ['A1:F1', false],
        ];
    }
}
