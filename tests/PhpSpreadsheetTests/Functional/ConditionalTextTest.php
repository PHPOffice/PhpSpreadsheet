<?php

namespace PhpOffice\PhpSpreadsheetTests\Functional;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Conditional;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ConditionalTextTest extends AbstractFunctional
{
    const COLOR_GREEN = 'FF99FF66';
    const COLOR_RED = 'FFFF5050';
    const COLOR_BLUE = 'FF5050FF';
    const COLOR_YELLOW = 'FFFFFF50';

    public function testConditionalText(): void
    {
        $format = 'Xlsx';
        $spreadsheet = new Spreadsheet();

        $conditionalStyles = [];
        // if text contains 'anywhere' - red background
        $condition0 = new Conditional();
        $condition0->setConditionType(Conditional::CONDITION_CONTAINSTEXT);
        $condition0->setOperatorType(Conditional::CONDITION_CONTAINSTEXT);
        $condition0->setText('anywhere');
        $condition0->getStyle()->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getEndColor()->setARGB(self::COLOR_RED);
        array_push($conditionalStyles, $condition0);

        // if text contains 'Left' on left - green background
        $condition1 = new Conditional();
        $condition1->setConditionType(Conditional::CONDITION_CONTAINSTEXT);
        $condition1->setOperatorType(Conditional::OPERATOR_BEGINSWITH);
        $condition1->setText('Left');
        $condition1->getStyle()->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getEndColor()->setARGB(self::COLOR_GREEN);
        array_push($conditionalStyles, $condition1);

        // if text contains 'right' on right - blue background
        $condition2 = new Conditional();
        $condition2->setConditionType(Conditional::CONDITION_CONTAINSTEXT);
        $condition2->setOperatorType(Conditional::OPERATOR_ENDSWITH);
        $condition2->setText('right');
        $condition2->getStyle()->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getEndColor()->setARGB(self::COLOR_BLUE);
        array_push($conditionalStyles, $condition2);

        // if text contains no spaces - yellow background
        $condition3 = new Conditional();
        $condition3->setConditionType(Conditional::CONDITION_CONTAINSTEXT);
        $condition3->setOperatorType(Conditional::OPERATOR_NOTCONTAINS);
        $condition3->setText(' ');
        $condition3->getStyle()->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getEndColor()->setARGB(self::COLOR_YELLOW);
        array_push($conditionalStyles, $condition3);
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('B1', 'This should match anywhere, right?');
        $sheet->setCellValue('B2', 'This should match nowhere, right?');
        $sheet->setCellValue('B3', 'Left match');
        $sheet->setCellValue('B4', 'Match on right');
        $sheet->setCellValue('B5', 'nospaces');
        $xpCoordinate = 'B1:B5';

        $spreadsheet->getActiveSheet()->setConditionalStyles($xpCoordinate, $conditionalStyles);
        $sheet->getColumnDimension('B')->setAutoSize(true);

        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, $format);

        // see if we successfully written conditional text elements
        $newConditionalStyles = $reloadedSpreadsheet->getActiveSheet()->getConditionalStyles($xpCoordinate);
        $cnt = count($conditionalStyles);
        for ($i = 0; $i < $cnt; ++$i) {
            self::assertEquals(
                $conditionalStyles[$i]->getConditionType(),
                $newConditionalStyles[$i]->getConditionType(),
                "Failure on condition type $i"
            );
            self::assertEquals(
                $conditionalStyles[$i]->getOperatorType(),
                $newConditionalStyles[$i]->getOperatorType(),
                "Failure on operator type $i"
            );
            self::assertEquals(
                $conditionalStyles[$i]->getText(),
                $newConditionalStyles[$i]->getText(),
                "Failure on text $i"
            );
            $filCond = $conditionalStyles[$i]->getStyle()->getFill();
            $newCond = $newConditionalStyles[$i]->getStyle()->getFill();
            self::assertEquals(
                $filCond->getFillType(),
                $newCond->getFillType(),
                "Failure on fill type $i"
            );
            self::assertEquals(
                $filCond->getEndColor()->getARGB(),
                $newCond->getEndColor()->getARGB(),
                "Failure on end color $i"
            );
        }
    }
}
