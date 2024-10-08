<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Xls;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Conditional;
use PhpOffice\PhpSpreadsheet\Style\Style;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class ConditionalLimitsTest extends AbstractFunctional
{
    public function testLimits(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray(
            [
                ['Cell', 0, null, null, 'Col Rng', -2, -1],
                [null, null, null, null, null, 0, 1],
                ['Cell Rng', -2, -1, 0, null, 2, 3],
                [null, 1, 2, 3, null, 4, -1],
                [],
                ['Row Rng'],
                [-2, -1, 0],
                [1, 2, 3],
            ],
            strictNullComparison: true
        );
        $redStyle = new Style(false, true);
        $redStyle->getFont()->setColor(new Color(Color::COLOR_RED));

        $condition1 = new Conditional();
        $condition1->setConditionType(Conditional::CONDITION_CELLIS)
            ->setOperatorType(Conditional::OPERATOR_BETWEEN)
            ->addCondition(-1)
            ->addCondition(1)
            ->setStyle($redStyle);
        $conditionalStyles = [$condition1];
        $cellRange = 'B1';
        $sheet->getStyle($cellRange)->setConditionalStyles($conditionalStyles);

        $condition2 = new Conditional();
        $condition2->setConditionType(Conditional::CONDITION_CELLIS)
            ->setOperatorType(Conditional::OPERATOR_BETWEEN)
            ->addCondition(-1.5)
            ->addCondition(1.5)
            ->setStyle($redStyle);
        $conditionalStyles = [$condition2];
        $cellRange = 'F:G';
        $sheet->getStyle($cellRange)->setConditionalStyles($conditionalStyles);

        $condition3 = new Conditional();
        $condition3->setConditionType(Conditional::CONDITION_CELLIS)
            ->setOperatorType(Conditional::OPERATOR_BETWEEN)
            ->addCondition(-1)
            ->addCondition(70000)
            ->setStyle($redStyle);
        $conditionalStyles = [$condition3];
        $cellRange = '7:8';
        $sheet->getStyle($cellRange)->setConditionalStyles($conditionalStyles);

        $cellRange = 'B3:D4';
        $sheet->getStyle($cellRange)->setConditionalStyles($conditionalStyles);
        $sheet->setSelectedCells('A1');
        $keys = array_keys($sheet->getConditionalStylesCollection());
        self::assertSame(['B1', 'F1:G1048576', 'A7:XFD8', 'B3:D4'], $keys);

        $robj = $this->writeAndReload($spreadsheet, 'Xls');
        $spreadsheet->disconnectWorksheets();
        $sheet0 = $robj->getActiveSheet();
        $conditionals = $sheet0->getConditionalStylesCollection();
        self::assertSame(['B1', 'F1:G65536', 'A7:IV8', 'B3:D4'], array_keys($conditionals));
        $b1 = $conditionals['B1'][0];
        self::assertSame([-1, 1], $b1->getConditions());
        $b1 = $conditionals['F1:G65536'][0];
        self::assertSame([-1.5, 1.5], $b1->getConditions());
        $b1 = $conditionals['A7:IV8'][0];
        self::assertSame([-1, 70000], $b1->getConditions());
        $b1 = $conditionals['B3:D4'][0];
        self::assertSame([-1, 70000], $b1->getConditions());
        $robj->disconnectWorksheets();
    }
}
