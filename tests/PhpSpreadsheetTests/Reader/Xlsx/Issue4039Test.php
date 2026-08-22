<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Conditional;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class Issue4039Test extends AbstractFunctional
{
    private static string $testbook = 'tests/data/Style/ConditionalFormatting/CellMatcher.xlsx';

    public function testUnionRange(): void
    {
        $reader = new Xlsx();
        $spreadsheet = $reader->load(self::$testbook);
        $sheet = $spreadsheet->getSheetByNameOrThrow('cellIs Expression');
        $expected = [
            'A12:D17,A20', // split range
            'A22:D27',
            'A2:E6',
        ];
        self::assertSame($expected, array_keys($sheet->getConditionalStylesCollection()));
        self::assertSame($expected[0], $sheet->getConditionalRange('A20'));
        self::assertSame($expected[0], $sheet->getConditionalRange('C15'));
        self::assertNull($sheet->getConditionalRange('A19'));
        self::assertSame($expected[1], $sheet->getConditionalRange('D25'));
        $spreadsheet->disconnectWorksheets();
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
        // Writer will change this range to equivalent 'B1,B2,B3'
        $sheet->setConditionalStyles('A1:C3 B1:B3', $conditionalStyles);
        $robj = $this->writeAndReload($spreadsheet, 'Xlsx');
        $spreadsheet->disconnectWorksheets();
        $sheet0 = $robj->getActiveSheet();
        $conditionals = $sheet0->getConditionalStylesCollection();
        self::assertSame(['B1,B2,B3'], array_keys($conditionals));
        $cond1 = $conditionals['B1,B2,B3'][0];
        self::assertSame(Conditional::CONDITION_CELLIS, $cond1->getConditionType());
        self::assertSame(Conditional::OPERATOR_BETWEEN, $cond1->getOperatorType());
        self::assertSame(['2', '3'], $cond1->getConditions());
        $font1 = $cond1->getStyle()->getFont();
        self::assertTrue($font1->getBold());
        $robj->disconnectWorksheets();
    }
}
