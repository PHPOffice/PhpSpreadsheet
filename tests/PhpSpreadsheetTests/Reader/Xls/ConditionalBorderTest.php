<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xls;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Conditional;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class ConditionalBorderTest extends AbstractFunctional
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
        $condition1->getStyle()->getBorders()->getRight()
            ->setBorderStyle(Border::BORDER_THICK)
            ->getColor()
            ->setArgb('FFFF0000');
        $condition1->getStyle()->getBorders()->getBottom()
            ->setBorderStyle(Border::BORDER_DASHED)
            ->getColor()
            ->setArgb('FFF08000');

        $condition2 = new Conditional();
        $condition2->setConditionType(Conditional::CONDITION_CELLIS);
        $condition2->setOperatorType(Conditional::OPERATOR_GREATERTHAN);
        $condition2->addCondition(5);
        $condition2->getStyle()->getBorders()->getRight()
            ->setBorderStyle(Border::BORDER_THICK)
            ->getColor()
            ->setArgb('FF0000FF');
        $condition2->getStyle()->getBorders()->getBottom()
            ->setBorderStyle(Border::BORDER_DASHED)
            ->getColor()
            ->setArgb('FF0080FF');

        $conditionalStyles = [$condition1, $condition2];
        $sheet->getStyle('A1:A5')->setConditionalStyles($conditionalStyles);

        $sheet->getCell('C6')->setValue(1);
        $sheet->getCell('C7')->setValue(9);
        $sheet->getCell('C8')->setValue(5);
        $sheet->getCell('C9')->setValue(2);
        $sheet->getCell('C10')->setValue(8);

        $condition1 = new Conditional();
        $condition1->setConditionType(Conditional::CONDITION_CELLIS);
        $condition1->setOperatorType(Conditional::OPERATOR_LESSTHAN);
        $condition1->addCondition(5);
        $condition1->getStyle()->getBorders()->getLeft()
            ->setBorderStyle(Border::BORDER_THICK)
            ->getColor()
            ->setArgb('FFFF0000');
        $condition1->getStyle()->getBorders()->getTop()
            ->setBorderStyle(Border::BORDER_DASHED)
            ->getColor()
            ->setArgb('FFF08000');

        $condition2 = new Conditional();
        $condition2->setConditionType(Conditional::CONDITION_CELLIS);
        $condition2->setOperatorType(Conditional::OPERATOR_GREATERTHAN);
        $condition2->addCondition(5);
        $condition2->getStyle()->getBorders()->getLeft()
            ->setBorderStyle(Border::BORDER_THICK)
            ->getColor()
            ->setArgb('FF0000FF');
        $condition2->getStyle()->getBorders()->getTop()
            ->setBorderStyle(Border::BORDER_DASHED)
            ->getColor()
            ->setArgb('FF0080FF');

        $conditionalStyles = [$condition1, $condition2];
        $sheet->getStyle('C6:C10')->setConditionalStyles($conditionalStyles);

        $newSpreadsheet = $this->writeAndReload($spreadsheet, 'Xls');
        $spreadsheet->disconnectWorksheets();
        $sheet = $newSpreadsheet->getActiveSheet();
        $conditionals = $sheet->getConditionalStylesCollection();
        self::assertCount(2, $conditionals);

        $cond1 = $conditionals['A1:A5'];
        self::assertCount(2, $cond1);

        $borders = $cond1[0]->getStyle()->getBorders();
        self::assertSame(Border::BORDER_OMIT, $borders->getLeft()->getBorderStyle());
        self::assertSame(Border::BORDER_THICK, $borders->getRight()->getBorderStyle());
        self::assertSame('FFFF0000', $borders->getRight()->getColor()->getARGB());
        self::assertSame(Border::BORDER_OMIT, $borders->getTop()->getBorderStyle());
        self::assertSame(Border::BORDER_DASHED, $borders->getBottom()->getBorderStyle());
        self::assertSame('FFF08000', $borders->getBottom()->getColor()->getARGB());

        $borders = $cond1[1]->getStyle()->getBorders();
        self::assertSame(Border::BORDER_OMIT, $borders->getLeft()->getBorderStyle());
        self::assertSame(Border::BORDER_THICK, $borders->getRight()->getBorderStyle());
        self::assertSame('FF0000FF', $borders->getRight()->getColor()->getARGB());
        self::assertSame(Border::BORDER_OMIT, $borders->getTop()->getBorderStyle());
        self::assertSame(Border::BORDER_DASHED, $borders->getBottom()->getBorderStyle());
        self::assertSame('FF0080FF', $borders->getBottom()->getColor()->getARGB());

        $cond1 = $conditionals['C6:C10'];
        self::assertCount(2, $cond1);

        $borders = $cond1[0]->getStyle()->getBorders();
        self::assertSame(Border::BORDER_THICK, $borders->getLeft()->getBorderStyle());
        self::assertSame('FFFF0000', $borders->getLeft()->getColor()->getARGB());
        self::assertSame(Border::BORDER_OMIT, $borders->getRight()->getBorderStyle());
        self::assertSame(Border::BORDER_DASHED, $borders->getTop()->getBorderStyle());
        self::assertSame('FFF08000', $borders->getTop()->getColor()->getARGB());
        self::assertSame(Border::BORDER_OMIT, $borders->getBottom()->getBorderStyle());

        $borders = $cond1[1]->getStyle()->getBorders();
        self::assertSame(Border::BORDER_THICK, $borders->getLeft()->getBorderStyle());
        self::assertSame('FF0000FF', $borders->getLeft()->getColor()->getARGB());
        self::assertSame(Border::BORDER_OMIT, $borders->getRight()->getBorderStyle());
        self::assertSame(Border::BORDER_DASHED, $borders->getTop()->getBorderStyle());
        self::assertSame('FF0080FF', $borders->getTop()->getColor()->getARGB());
        self::assertSame(Border::BORDER_OMIT, $borders->getBottom()->getBorderStyle());

        $newSpreadsheet->disconnectWorksheets();
    }
}
