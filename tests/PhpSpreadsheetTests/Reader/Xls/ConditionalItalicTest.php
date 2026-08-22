<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xls;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Conditional;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class ConditionalItalicTest extends AbstractFunctional
{
    public function testFormulas(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getCell('A1')->setValue(1);
        $sheet->getCell('A2')->setValue(9);
        $sheet->getCell('A3')->setValue(5);
        $sheet->getCell('A4')->setValue(2);
        $sheet->getCell('A5')->setValue(8);

        $condition1 = new Conditional();
        $condition1->setConditionType(Conditional::CONDITION_CELLIS);
        $condition1->setOperatorType(Conditional::OPERATOR_LESSTHAN);
        $condition1->addCondition(5);
        $condition1->getStyle()->getFont()->setItalic(true);

        $condition2 = new Conditional();
        $condition2->setConditionType(Conditional::CONDITION_CELLIS);
        $condition2->setOperatorType(Conditional::OPERATOR_GREATERTHAN);
        $condition2->addCondition(5);
        $condition2->getStyle()->getFont()->setStrikeThrough(true)->setBold(true);

        $conditionalStyles = [$condition1, $condition2];
        $sheet->getStyle('A1:A5')->setConditionalStyles($conditionalStyles);

        $newSpreadsheet = $this->writeAndReload($spreadsheet, 'Xls');
        $spreadsheet->disconnectWorksheets();
        $sheet = $newSpreadsheet->getActiveSheet();
        $conditionals = $sheet->getConditionalStylesCollection();
        self::assertCount(1, $conditionals);

        $cond1 = $conditionals['A1:A5'];
        self::assertCount(2, $cond1);

        $font = $cond1[0]->getStyle()->getFont();
        self::assertTrue($font->getItalic());
        self::assertNull($font->getBold());
        self::assertNull($font->getStrikethrough());

        $font = $cond1[1]->getStyle()->getFont();
        self::assertNull($font->getItalic());
        self::assertTrue($font->getBold());
        self::assertTrue($font->getStrikethrough());

        $newSpreadsheet->disconnectWorksheets();
    }
}
