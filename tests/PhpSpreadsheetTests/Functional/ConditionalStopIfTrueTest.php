<?php

namespace PhpOffice\PhpSpreadsheetTests\Functional;

use PhpOffice\PhpSpreadsheet\Spreadsheet;

class ConditionalStopIfTrueTest extends AbstractFunctional
{
    const COLOR_GREEN = 'FF99FF66';
    const COLOR_RED = 'FFFF5050';

    public function providerFormats(): array
    {
        return [
            ['Xlsx'],
        ];
    }

    /**
     * @dataProvider providerFormats
     *
     * @param string $format
     */
    public function testConditionalStopIfTrue($format): void
    {
        $pCoordinate = 'A1:A3';

        // if blank cell -> no styling
        $condition0 = new \PhpOffice\PhpSpreadsheet\Style\Conditional();
        $condition0->setConditionType(\PhpOffice\PhpSpreadsheet\Style\Conditional::CONDITION_EXPRESSION);
        $condition0->addCondition('LEN(TRIM(A1))=0');
        $condition0->setStopIfTrue(true); // ! stop here

        // if value below 0.6 (matches also blank cells!) -> red background
        $condition1 = new \PhpOffice\PhpSpreadsheet\Style\Conditional();
        $condition1->setConditionType(\PhpOffice\PhpSpreadsheet\Style\Conditional::CONDITION_CELLIS);
        $condition1->setOperatorType(\PhpOffice\PhpSpreadsheet\Style\Conditional::OPERATOR_LESSTHAN);
        $condition1->addCondition(0.6);
        $condition1->getStyle()->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getEndColor()->setARGB(self::COLOR_RED);

        // if value above 0.6 -> green background
        $condition2 = new \PhpOffice\PhpSpreadsheet\Style\Conditional();
        $condition2->setConditionType(\PhpOffice\PhpSpreadsheet\Style\Conditional::CONDITION_CELLIS);
        $condition2->setOperatorType(\PhpOffice\PhpSpreadsheet\Style\Conditional::OPERATOR_GREATERTHAN);
        $condition2->addCondition(0.6);
        $condition2->getStyle()->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getEndColor()->setARGB(self::COLOR_GREEN);

        $spreadsheet = new Spreadsheet();
        $spreadsheet->getActiveSheet()->getCell('A1')->setValue(0.7);
        $spreadsheet->getActiveSheet()->getCell('A2')->setValue('');
        $spreadsheet->getActiveSheet()->getCell('A3')->setValue(0.4);

        // put all three conditions in sheet
        $conditionalStyles = [];
        array_push($conditionalStyles, $condition0);
        array_push($conditionalStyles, $condition1);
        array_push($conditionalStyles, $condition2);
        $spreadsheet->getActiveSheet()->setConditionalStyles($pCoordinate, $conditionalStyles);

        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, $format);

        // see if we successfully written "StopIfTrue"
        $newConditionalStyles = $reloadedSpreadsheet->getActiveSheet()->getConditionalStyles($pCoordinate);
        self::assertTrue($newConditionalStyles[0]->getStopIfTrue(), 'StopIfTrue should be set (=true) on first condition');
        self::assertFalse($newConditionalStyles[1]->getStopIfTrue(), 'StopIfTrue should not be set (=false) on second condition');
        self::assertFalse($newConditionalStyles[2]->getStopIfTrue(), 'StopIfTrue should not be set (=false) on third condition');
    }
}
