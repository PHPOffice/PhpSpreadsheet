<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Worksheet;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Conditional;
use PHPUnit\Framework\TestCase;

class ConditionalIntersectionTest extends TestCase
{
    public function testGetConditionalStyles(): void
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
        $sheet->setConditionalStyles('A1:C3 B1:B3', $conditionalStyles);
        self::assertEmpty($sheet->getConditionalStyles('A2'));
        $cond = $sheet->getConditionalStyles('B2');
        self::assertCount(1, $cond);
        self::assertSame(Conditional::CONDITION_CELLIS, $cond[0]->getConditionType());
        self::assertSame(Conditional::OPERATOR_BETWEEN, $cond[0]->getOperatorType());
        self::assertSame([2, 3], $cond[0]->getConditions());
        self::assertTrue($cond[0]->getStyle()->getFont()->getBold());
        $spreadsheet->disconnectWorksheets();
    }

    public function testGetConditionalRange(): void
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
        $sheet->setConditionalStyles('A1:C3 B1:B3', $conditionalStyles);
        self::assertNull($sheet->getConditionalRange('A2'));
        self::assertSame('A1:C3 B1:B3', $sheet->getConditionalRange('B2'));
        $spreadsheet->disconnectWorksheets();
    }
}
