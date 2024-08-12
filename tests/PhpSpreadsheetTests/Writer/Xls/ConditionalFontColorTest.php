<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Xls;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Conditional;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class ConditionalFontColorTest extends AbstractFunctional
{
    public function testConditionalFontColor(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getCell('A1')->setValue(10);
        $condition1 = new Conditional();
        $condition1->setConditionType(Conditional::CONDITION_CELLIS);
        $condition1->setOperatorType(Conditional::OPERATOR_LESSTHAN);
        $condition1->addCondition(20);
        $condition1->getStyle()->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FF2345FA');
        $condition1->getStyle()->getFont()
            ->setBold(true);
        $condition1->getStyle()->getFont()->getColor()
            ->setARGB('FFFF8193');
        $conditionalStyles = [$condition1];
        $sheet->getStyle('A1')->setConditionalStyles($conditionalStyles);

        $robj = $this->writeAndReload($spreadsheet, 'Xls');
        $spreadsheet->disconnectWorksheets();
        $sheet0 = $robj->setActiveSheetIndex(0);
        $conditionals = $sheet0->getConditionalStylesCollection();
        self::assertCount(1, $conditionals);
        self::assertArrayHasKey('A1', $conditionals);
        $font = $conditionals['A1'][0]->getStyle()->getFont();
        self::assertSame('FFFF8193', $font->getColor()->getARGB());
        $fill = $conditionals['A1'][0]->getStyle()->getFill();
        self::assertSame('FF2345FA', $fill->getStartColor()->getARGB());
        self::assertSame(Fill::FILL_SOLID, $fill->getFillType());
        $robj->disconnectWorksheets();
    }
}
