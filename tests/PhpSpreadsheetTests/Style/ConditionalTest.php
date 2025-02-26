<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Style;

use PhpOffice\PhpSpreadsheet\Style\Conditional;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PHPUnit\Framework\TestCase;

class ConditionalTest extends TestCase
{
    public function testClone(): void
    {
        $condition1 = new Conditional();
        $condition1->setConditionType(Conditional::CONDITION_CELLIS);
        $condition1->setOperatorType(Conditional::OPERATOR_LESSTHAN);
        $condition1->addCondition(0.6);
        $condition1->getStyle()->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFFF0000');
        $conditionclone = clone $condition1;
        self::AssertEquals($condition1, $conditionclone);
        self::AssertEquals($condition1->getStyle(), $conditionclone->getStyle());
        self::AssertNotSame($condition1->getStyle(), $conditionclone->getStyle());
    }

    public function testVariousAdds(): void
    {
        $condition1 = new Conditional();
        $condition1->setConditionType(Conditional::CONDITION_CELLIS);
        $condition1->setOperatorType(Conditional::OPERATOR_LESSTHAN);
        $condition1->addCondition(0.6);
        $condition1->getStyle()->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFFF0000');
        $condition2 = new Conditional();
        $condition2->setConditionType(Conditional::CONDITION_CELLIS);
        $condition2->setOperatorType(Conditional::OPERATOR_LESSTHAN);
        $condition2->setConditions(0.6);
        $condition2->getStyle()->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFFF0000');
        $condition3 = new Conditional();
        $condition3->setConditionType(Conditional::CONDITION_CELLIS);
        $condition3->setOperatorType(Conditional::OPERATOR_LESSTHAN);
        $condition3->setConditions([0.6]);
        $condition3->getStyle()->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFFF0000');
        self::AssertEquals($condition1, $condition2);
        self::AssertEquals($condition1, $condition3);
    }
}
