<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Conditional;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class ConditionalPriorityTest extends AbstractFunctional
{
    public function testConditionalPriority(): void
    {
        $filename = 'tests/data/Reader/XLSX/issue.4312c.xlsx';
        $reader = IOFactory::createReader('Xlsx');
        $spreadsheet = $reader->load($filename);
        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, 'Xlsx');
        $spreadsheet->disconnectWorksheets();
        $worksheet = $reloadedSpreadsheet->getActiveSheet();
        $priorities = [];
        foreach ($worksheet->getConditionalStylesCollection() as $conditionalStyles) {
            foreach ($conditionalStyles as $conditional) {
                $priorities[] = $conditional->getPriority();
            }
        }
        $expected = [27, 2, 3, 4, 1, 22, 14, 5, 6, 7, 20];
        self::assertSame($expected, $priorities);
        $reloadedSpreadsheet->disconnectWorksheets();
    }

    public function testZeroPriority(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray([
            [1, 1, 1, 1],
            [2, 2, 2, 2],
            [3, 3, 3, 3],
            [4, 4, 4, 4],
            [5, 5, 5, 5],
        ]);

        $range = 'A1:A5';
        $styles = [];
        $new = new Conditional();
        $new->setConditionType(Conditional::CONDITION_CELLIS)
            ->setOperatorType(Conditional::OPERATOR_EQUAL)
            ->setPriority(30)
            ->setConditions(['3'])
            ->getStyle()
            ->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()
            ->setArgb('FFC00000');
        $styles[] = $new;
        $sheet->setConditionalStyles($range, $styles);

        $range = 'B1:B5';
        $styles = [];
        $new = new Conditional();
        $new->setConditionType(Conditional::CONDITION_EXPRESSION)
            ->setConditions('=MOD(A1,2)=0')
            ->getStyle()
            ->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()
            ->setArgb('FF00B0F0');
        $styles[] = $new;
        $new = new Conditional();
        $new->setConditionType(Conditional::CONDITION_CELLIS)
            ->setOperatorType(Conditional::OPERATOR_EQUAL)
            ->setPriority(40)
            ->setConditions(['4'])
            ->getStyle()
            ->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()
            ->setArgb('FFFFC000');
        $styles[] = $new;
        $sheet->setConditionalStyles($range, $styles);

        $range = 'C1:C5';
        $styles = [];
        $new = new Conditional();
        $new->setConditionType(Conditional::CONDITION_CELLIS)
            ->setOperatorType(Conditional::OPERATOR_EQUAL)
            ->setPriority(20)
            ->setConditions(['2'])
            ->getStyle()
            ->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()
            ->setArgb('FFFFFF00');
        $styles[] = $new;
        $new = new Conditional();
        $new->setConditionType(Conditional::CONDITION_CELLIS)
            ->setOperatorType(Conditional::OPERATOR_EQUAL)
            ->setConditions(['5'])
            ->getStyle()
            ->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()
            ->setArgb('FF008080');
        $styles[] = $new;
        $sheet->setConditionalStyles($range, $styles);

        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, 'Xlsx');
        $spreadsheet->disconnectWorksheets();
        $worksheet = $reloadedSpreadsheet->getActiveSheet();
        $priorities = [];
        foreach ($worksheet->getConditionalStylesCollection() as $conditionalStyles) {
            foreach ($conditionalStyles as $conditional) {
                $priorities[] = $conditional->getPriority();
            }
        }
        // B1:B5 is written in order 41, 40, but Reader sorts them
        $expected = [30, 40, 41, 20, 42];
        self::assertSame($expected, $priorities);
        $styles = $worksheet->getConditionalStyles('B1:B5');
        self::assertSame(Conditional::CONDITION_CELLIS, $styles[0]->getConditionType());
        self::assertSame(40, $styles[0]->getPriority());
        self::assertSame(Conditional::CONDITION_EXPRESSION, $styles[1]->getConditionType());
        self::assertSame(41, $styles[1]->getPriority());
        $reloadedSpreadsheet->disconnectWorksheets();
    }
}
