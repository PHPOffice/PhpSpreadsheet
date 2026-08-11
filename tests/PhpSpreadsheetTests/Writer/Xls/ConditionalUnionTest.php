<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Xls;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Conditional;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class ConditionalUnionTest extends AbstractFunctional
{
    public function testConditionalUnion(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray([
            [1, 2, 3, 4, 5],
            [2, 3, 4, 5, 6],
            [3, 4, 5, 6, 7],
        ]);
        $condition1 = new Conditional();
        $condition1->setConditionType(Conditional::CONDITION_CELLIS);
        $condition1->setOperatorType(Conditional::OPERATOR_BETWEEN);
        $condition1->setConditions([2, 4]);
        $condition1->getStyle()->getFont()
            ->setBold(true);
        $conditionalStyles = [$condition1];
        $sheet->setConditionalStyles('A1:A3,C1:E3', $conditionalStyles);

        $robj = $this->writeAndReload($spreadsheet, 'Xls');
        $spreadsheet->disconnectWorksheets();
        $sheet0 = $robj->getActiveSheet();
        $conditionals = $sheet0->getConditionalStylesCollection();
        self::assertSame(['A1:A3', 'C1:E3'], array_keys($conditionals));
        $cond1 = $conditionals['A1:A3'][0];
        self::assertSame(Conditional::CONDITION_CELLIS, $cond1->getConditionType());
        self::assertSame(Conditional::OPERATOR_BETWEEN, $cond1->getOperatorType());
        self::assertSame([2, 4], $cond1->getConditions());
        $font1 = $cond1->getStyle()->getFont();
        self::assertTrue($font1->getBold());

        $cond2 = $conditionals['C1:E3'][0];
        self::assertSame(Conditional::CONDITION_CELLIS, $cond2->getConditionType());
        self::assertSame(Conditional::OPERATOR_BETWEEN, $cond2->getOperatorType());
        self::assertSame([2, 4], $cond2->getConditions());
        $font2 = $cond2->getStyle()->getFont();
        self::assertTrue($font2->getBold());
        $robj->disconnectWorksheets();
    }

    public function testIntersectionRange(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray([
            [1, 2, 3, 4, 5],
            [2, 3, 4, 5, 6],
            [3, 4, 5, 6, 7],
        ]);
        $condition1 = new Conditional();
        $condition1->setConditionType(Conditional::CONDITION_CELLIS);
        $condition1->setOperatorType(Conditional::OPERATOR_BETWEEN);
        $condition1->setConditions([2, 3]);
        $condition1->getStyle()->getFont()
            ->setBold(true);
        $conditionalStyles = [$condition1];
        $sheet->setConditionalStyles('A1:B5,D1:E5 B2:D4', $conditionalStyles);
        $robj = $this->writeAndReload($spreadsheet, 'Xls');
        $spreadsheet->disconnectWorksheets();
        $sheet0 = $robj->getActiveSheet();
        $conditionals = $sheet0->getConditionalStylesCollection();
        self::assertSame(['A1:B5', 'D2', 'D3', 'D4'], array_keys($conditionals));

        $cond1 = $conditionals['A1:B5'][0];
        self::assertSame(Conditional::CONDITION_CELLIS, $cond1->getConditionType());
        self::assertSame(Conditional::OPERATOR_BETWEEN, $cond1->getOperatorType());
        self::assertSame([2, 3], $cond1->getConditions());
        $font1 = $cond1->getStyle()->getFont();
        self::assertTrue($font1->getBold());

        $cond2 = $conditionals['D2'][0];
        self::assertSame(Conditional::CONDITION_CELLIS, $cond2->getConditionType());
        self::assertSame(Conditional::OPERATOR_BETWEEN, $cond2->getOperatorType());
        self::assertSame([2, 3], $cond2->getConditions());
        $font2 = $cond2->getStyle()->getFont();
        self::assertTrue($font2->getBold());
        $robj->disconnectWorksheets();
    }
}
