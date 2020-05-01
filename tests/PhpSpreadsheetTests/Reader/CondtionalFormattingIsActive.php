<?php

namespace PhpOffice\PhpSpreadsheetTests\Reader;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PHPUnit\Framework\TestCase;

class CondtionalFormattingIsActive extends TestCase
{
    public function testCondtionalformattingIsActive()
    {
        $filename = './data/Reader/XLSX/ConditionalFormattingIsActiveTest.xlsx';
        $reader = new Xlsx();
        $spreadsheet = $reader->load($filename);
        $worksheet = $spreadsheet->getActiveSheet();
        $conditionalFormattings = $worksheet->getConditionalStylesCollection();
        $calcer = Calculation::getInstance($spreadsheet);
        $calcer->disableCalculationCache();
        $precision = 8;

        // Taken from Excel 2113 manualy
        $should = [false, true, true, false, true, false, false, true, true, false, false, true, true, false, false, true, true, false, true, false, false, false, true, true];

        $actual = [];

        self::assertTrue(isset($conditionalFormattings));
        self::assertTrue(count($conditionalFormattings) > 0);

        foreach ($conditionalFormattings as $key => $formatings) {
            $split = explode(':', $key);
            $col = ord(substr($split[0], 0, 1));
            $multuseCol = false;

            if (count($split) > 1) {
                $colEnd = ord(substr($split[0], 0, 1));
            } else {
                $colEnd = $col;
            }

            $row = substr($split[0], 1);

            if (count($split) > 1) {
                $rowEnd = substr($split[1], 1);
            } else {
                $rowEnd = $row;
            }

            $multuseRow = false;
            if (isset($formatings) && count($formatings) > 0) {
                for ($i = $col; $i <= $colEnd; ++$i) {
                    for ($j = $row; $j <= $rowEnd; ++$j) {
                        foreach ($formatings as $formating) {
                            if ($col != $colEnd) {
                                $multuseCol = ($col - $i) * (-1);
                            }
                            if ($row != $rowEnd) {
                                $multuseRow = ($row - $j) * (-1);
                            }

                            $cell = $worksheet->getCell(chr($i) . $j);

                            $active = $formating->isActive($calcer, $cell, $precision, $multuseCol, $multuseRow);
                            $actual[$j - 1] = $active;
                        }
                    }
                }
            }
        }
        $count = count($should);
        for ($i = 0; $i < $count; ++$i) {
            self::assertEquals($should[$i], $actual[$i]);
        }
    }

    public function testCondtionalformattingIsActiveWithOutFile()
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $source = [[0.0, 4.066, 14.44, 14.44, 17.50, 'not beetween'],
                            [249.9, 5.241, 271.47, 21.58, 17.50, 'not beetween'],
                            [260.0, 5.241, 271.47, 11.47, 17.50, 'beetween'],
                            [800.0, 5.241, 271.47, -528.53, 17.50, 'beetween'],
                            [232.5, 5.241, 250.00, 17.50, 17.50, '='],
                            [280.0, 5.241, 250.00, -30.00, 17.50, '='],
                            [232.5, 5.241, 250.00, 17.50, 17.50, '!='],
                            [280.0, 5.241, 250.00, -30.00, 17.50, '!='],
                            [210.0, 5.241, 250.00, 40.00, 17.50, '>'],
                            [280.0, 5.241, 250.00, -30.00, 17.50, '>'],
                            [210.0, 5.241, 250.00, 40.00, 17.50, '<'],
                            [280.0, 5.241, 250.00, -30.00, 17.50, '<'],
                            [210.0, 5.241, 250.00, 40.00, 17.50, '>='],
                            [280.0, 5.241, 250.00, -30.00, 17.50, '>='],
                            [210.0, 5.241, 250.00, 40.00, 17.50, '<='],
                            [280.0, 5.241, 250.00, -30.00, 17.50, '<='],
                            [232.5, 5.241, 250.00, 17.50, 17.50, '>='],
                            [280.0, 5.241, 250.00, -30.00, 17.50, '>='],
                            [232.5, 5.241, 250.00, 17.50, 17.50, '<='],
                            [241.0, 5.241, 250.00, 30.00, 17.50, '<='],
                            [232.5, 5.241, 250.00, 17.50, 17.50, 'not beetween'],
                            [232.5, 5.241, 250.00, -17.50, 17.50, 'not beetween'],
                            [232.5, 5.241, 250.00, 17.50, 17.50, 'beetween'],
                            [232.5, 5.241, 250.00, -17.50, 17.50, 'beetween'],
                        ];
        $worksheet = $spreadsheet->getActiveSheet();

        $worksheet->fromArray($source, null, 'A1', false);
        var_dump($worksheet->getCell('A1')->getValue());
        self::assertEquals(0.0, $worksheet->getCell('A1')->getValue());
        self::assertEquals('beetween', $worksheet->getCell('F24')->getValue());

        //Condition 1
        $conditional1 = new \PhpOffice\PhpSpreadsheet\Style\Conditional();
        $conditional1->setConditionType(\PhpOffice\PhpSpreadsheet\Style\Conditional::CONDITION_CELLIS);
        $conditional1->setOperatorType(\PhpOffice\PhpSpreadsheet\Style\Conditional::OPERATOR_NOTBETWEEN);
        $conditional1->addCondition('E1');
        $conditional1->addCondition('-E1');
        $conditional1->getStyle()->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_RED);
        $conditional1->getStyle()->getFont()->setBold(true);

        $conditionalStyles1 = $spreadsheet->getActiveSheet()->getStyle('D1:D2')->getConditionalStyles();
        $conditionalStyles1[] = $conditional1;
        $spreadsheet->getActiveSheet()->getStyle('D1:D2')->setConditionalStyles($conditionalStyles1);

        //Condition 2
        $conditional2 = new \PhpOffice\PhpSpreadsheet\Style\Conditional();
        $conditional2->setConditionType(\PhpOffice\PhpSpreadsheet\Style\Conditional::CONDITION_CELLIS);
        $conditional2->setOperatorType(\PhpOffice\PhpSpreadsheet\Style\Conditional::OPERATOR_BETWEEN);
        $conditional2->addCondition('E3');
        $conditional2->addCondition('-E3');
        $conditional2->getStyle()->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_RED);
        $conditional2->getStyle()->getFont()->setBold(true);

        $conditionalStyles2 = $spreadsheet->getActiveSheet()->getStyle('D3')->getConditionalStyles();
        $conditionalStyles2[] = $conditional2;
        $spreadsheet->getActiveSheet()->getStyle('D3')->setConditionalStyles($conditionalStyles2);

        //Condition 3
        $conditional3 = new \PhpOffice\PhpSpreadsheet\Style\Conditional();
        $conditional3->setConditionType(\PhpOffice\PhpSpreadsheet\Style\Conditional::CONDITION_CELLIS);
        $conditional3->setOperatorType(\PhpOffice\PhpSpreadsheet\Style\Conditional::OPERATOR_BETWEEN);
        $conditional3->addCondition('E4');
        $conditional3->addCondition('-E4');
        $conditional3->getStyle()->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_RED);
        $conditional3->getStyle()->getFont()->setBold(true);

        $conditionalStyles3 = $spreadsheet->getActiveSheet()->getStyle('D4')->getConditionalStyles();
        $conditionalStyles3[] = $conditional1;
        $spreadsheet->getActiveSheet()->getStyle('D4')->setConditionalStyles($conditionalStyles1);

        //Condition 4
        $conditional4 = new \PhpOffice\PhpSpreadsheet\Style\Conditional();
        $conditional4->setConditionType(\PhpOffice\PhpSpreadsheet\Style\Conditional::CONDITION_CELLIS);
        $conditional4->setOperatorType(\PhpOffice\PhpSpreadsheet\Style\Conditional::OPERATOR_EQUAL);
        $conditional4->addCondition('E5');
        $conditional4->getStyle()->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_RED);
        $conditional4->getStyle()->getFont()->setBold(true);

        $conditionalStyles4 = $spreadsheet->getActiveSheet()->getStyle('D5')->getConditionalStyles();
        $conditionalStyles4[] = $conditional4;
        $spreadsheet->getActiveSheet()->getStyle('D5')->setConditionalStyles($conditionalStyles4);

        //Condition 5
        $conditional5 = new \PhpOffice\PhpSpreadsheet\Style\Conditional();
        $conditional5->setConditionType(\PhpOffice\PhpSpreadsheet\Style\Conditional::CONDITION_CELLIS);
        $conditional5->setOperatorType(\PhpOffice\PhpSpreadsheet\Style\Conditional::OPERATOR_EQUAL);
        $conditional5->addCondition('E6');
        $conditional5->getStyle()->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_RED);
        $conditional5->getStyle()->getFont()->setBold(true);

        $conditionalStyles5 = $spreadsheet->getActiveSheet()->getStyle('D6')->getConditionalStyles();
        $conditionalStyles5[] = $conditional5;
        $spreadsheet->getActiveSheet()->getStyle('D6')->setConditionalStyles($conditionalStyles5);

        //Condition 6
        $conditional6 = new \PhpOffice\PhpSpreadsheet\Style\Conditional();
        $conditional6->setConditionType(\PhpOffice\PhpSpreadsheet\Style\Conditional::CONDITION_CELLIS);
        $conditional6->setOperatorType(\PhpOffice\PhpSpreadsheet\Style\Conditional::OPERATOR_NOTEQUAL);
        $conditional6->addCondition('E7');
        $conditional6->getStyle()->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_RED);
        $conditional6->getStyle()->getFont()->setBold(true);

        $conditionalStyles6 = $spreadsheet->getActiveSheet()->getStyle('D7')->getConditionalStyles();
        $conditionalStyles6[] = $conditional6;
        $spreadsheet->getActiveSheet()->getStyle('D7')->setConditionalStyles($conditionalStyles6);

        //Condition 7
        $conditional7 = new \PhpOffice\PhpSpreadsheet\Style\Conditional();
        $conditional7->setConditionType(\PhpOffice\PhpSpreadsheet\Style\Conditional::CONDITION_CELLIS);
        $conditional7->setOperatorType(\PhpOffice\PhpSpreadsheet\Style\Conditional::OPERATOR_NOTEQUAL);
        $conditional7->addCondition('E8');
        $conditional7->getStyle()->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_RED);
        $conditional7->getStyle()->getFont()->setBold(true);

        $conditionalStyles7 = $spreadsheet->getActiveSheet()->getStyle('D8')->getConditionalStyles();
        $conditionalStyles7[] = $conditional7;
        $spreadsheet->getActiveSheet()->getStyle('D8')->setConditionalStyles($conditionalStyles7);

        //Condition 8
        $conditional8 = new \PhpOffice\PhpSpreadsheet\Style\Conditional();
        $conditional8->setConditionType(\PhpOffice\PhpSpreadsheet\Style\Conditional::CONDITION_CELLIS);
        $conditional8->setOperatorType(\PhpOffice\PhpSpreadsheet\Style\Conditional::OPERATOR_GREATERTHAN);
        $conditional8->addCondition('E9');
        $conditional8->getStyle()->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_RED);
        $conditional8->getStyle()->getFont()->setBold(true);

        $conditionalStyles8 = $spreadsheet->getActiveSheet()->getStyle('D9')->getConditionalStyles();
        $conditionalStyles8[] = $conditional8;
        $spreadsheet->getActiveSheet()->getStyle('D9')->setConditionalStyles($conditionalStyles8);

        //Condition 9
        $conditional9 = new \PhpOffice\PhpSpreadsheet\Style\Conditional();
        $conditional9->setConditionType(\PhpOffice\PhpSpreadsheet\Style\Conditional::CONDITION_CELLIS);
        $conditional9->setOperatorType(\PhpOffice\PhpSpreadsheet\Style\Conditional::OPERATOR_GREATERTHAN);
        $conditional9->addCondition('E10');
        $conditional9->getStyle()->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_RED);
        $conditional9->getStyle()->getFont()->setBold(true);

        $conditionalStyles9 = $spreadsheet->getActiveSheet()->getStyle('D10')->getConditionalStyles();
        $conditionalStyles9[] = $conditional9;
        $spreadsheet->getActiveSheet()->getStyle('D10')->setConditionalStyles($conditionalStyles9);

        //Condition 10
        $conditional10 = new \PhpOffice\PhpSpreadsheet\Style\Conditional();
        $conditional10->setConditionType(\PhpOffice\PhpSpreadsheet\Style\Conditional::CONDITION_CELLIS);
        $conditional10->setOperatorType(\PhpOffice\PhpSpreadsheet\Style\Conditional::OPERATOR_LESSTHAN);
        $conditional10->addCondition('E11');
        $conditional10->getStyle()->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_RED);
        $conditional10->getStyle()->getFont()->setBold(true);

        $conditionalStyles10 = $spreadsheet->getActiveSheet()->getStyle('D11')->getConditionalStyles();
        $conditionalStyles10[] = $conditional10;
        $spreadsheet->getActiveSheet()->getStyle('D11')->setConditionalStyles($conditionalStyles10);

        //Condition 11
        $conditional11 = new \PhpOffice\PhpSpreadsheet\Style\Conditional();
        $conditional11->setConditionType(\PhpOffice\PhpSpreadsheet\Style\Conditional::CONDITION_CELLIS);
        $conditional11->setOperatorType(\PhpOffice\PhpSpreadsheet\Style\Conditional::OPERATOR_LESSTHAN);
        $conditional11->addCondition('E12');
        $conditional11->getStyle()->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_RED);
        $conditional11->getStyle()->getFont()->setBold(true);

        $conditionalStyles11 = $spreadsheet->getActiveSheet()->getStyle('D12')->getConditionalStyles();
        $conditionalStyles11[] = $conditional11;
        $spreadsheet->getActiveSheet()->getStyle('D12')->setConditionalStyles($conditionalStyles11);

        //Condition 12
        $conditional12 = new \PhpOffice\PhpSpreadsheet\Style\Conditional();
        $conditional12->setConditionType(\PhpOffice\PhpSpreadsheet\Style\Conditional::CONDITION_CELLIS);
        $conditional12->setOperatorType(\PhpOffice\PhpSpreadsheet\Style\Conditional::OPERATOR_GREATERTHANOREQUAL);
        $conditional12->addCondition('E13');
        $conditional12->getStyle()->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_RED);
        $conditional12->getStyle()->getFont()->setBold(true);

        $conditionalStyles12 = $spreadsheet->getActiveSheet()->getStyle('D13')->getConditionalStyles();
        $conditionalStyles12[] = $conditional12;
        $spreadsheet->getActiveSheet()->getStyle('D13')->setConditionalStyles($conditionalStyles12);

        //Condition 13
        $conditional13 = new \PhpOffice\PhpSpreadsheet\Style\Conditional();
        $conditional13->setConditionType(\PhpOffice\PhpSpreadsheet\Style\Conditional::CONDITION_CELLIS);
        $conditional13->setOperatorType(\PhpOffice\PhpSpreadsheet\Style\Conditional::OPERATOR_GREATERTHANOREQUAL);
        $conditional13->addCondition('E14');
        $conditional13->getStyle()->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_RED);
        $conditional13->getStyle()->getFont()->setBold(true);

        $conditionalStyles13 = $spreadsheet->getActiveSheet()->getStyle('D14')->getConditionalStyles();
        $conditionalStyles13[] = $conditional13;
        $spreadsheet->getActiveSheet()->getStyle('D14')->setConditionalStyles($conditionalStyles13);

        //Condition 14
        $conditional14 = new \PhpOffice\PhpSpreadsheet\Style\Conditional();
        $conditional14->setConditionType(\PhpOffice\PhpSpreadsheet\Style\Conditional::CONDITION_CELLIS);
        $conditional14->setOperatorType(\PhpOffice\PhpSpreadsheet\Style\Conditional::OPERATOR_LESSTHANOREQUAL);
        $conditional14->addCondition('E15');
        $conditional14->getStyle()->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_RED);
        $conditional14->getStyle()->getFont()->setBold(true);

        $conditionalStyles14 = $spreadsheet->getActiveSheet()->getStyle('D15')->getConditionalStyles();
        $conditionalStyles14[] = $conditional14;
        $spreadsheet->getActiveSheet()->getStyle('D15')->setConditionalStyles($conditionalStyles14);

        //Condition 15
        $conditional15 = new \PhpOffice\PhpSpreadsheet\Style\Conditional();
        $conditional15->setConditionType(\PhpOffice\PhpSpreadsheet\Style\Conditional::CONDITION_CELLIS);
        $conditional15->setOperatorType(\PhpOffice\PhpSpreadsheet\Style\Conditional::OPERATOR_LESSTHANOREQUAL);
        $conditional15->addCondition('E16');
        $conditional15->getStyle()->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_RED);
        $conditional15->getStyle()->getFont()->setBold(true);

        $conditionalStyles15 = $spreadsheet->getActiveSheet()->getStyle('D16')->getConditionalStyles();
        $conditionalStyles15[] = $conditional15;
        $spreadsheet->getActiveSheet()->getStyle('D16')->setConditionalStyles($conditionalStyles15);

        //Condition 16
        $conditional16 = new \PhpOffice\PhpSpreadsheet\Style\Conditional();
        $conditional16->setConditionType(\PhpOffice\PhpSpreadsheet\Style\Conditional::CONDITION_CELLIS);
        $conditional16->setOperatorType(\PhpOffice\PhpSpreadsheet\Style\Conditional::OPERATOR_GREATERTHANOREQUAL);
        $conditional16->addCondition('E17');
        $conditional16->getStyle()->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_RED);
        $conditional16->getStyle()->getFont()->setBold(true);

        $conditionalStyles16 = $spreadsheet->getActiveSheet()->getStyle('D17')->getConditionalStyles();
        $conditionalStyles16[] = $conditional16;
        $spreadsheet->getActiveSheet()->getStyle('D17')->setConditionalStyles($conditionalStyles16);

        //Condition 17
        $conditional17 = new \PhpOffice\PhpSpreadsheet\Style\Conditional();
        $conditional17->setConditionType(\PhpOffice\PhpSpreadsheet\Style\Conditional::CONDITION_CELLIS);
        $conditional17->setOperatorType(\PhpOffice\PhpSpreadsheet\Style\Conditional::OPERATOR_GREATERTHANOREQUAL);
        $conditional17->addCondition('E18');
        $conditional17->getStyle()->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_RED);
        $conditional17->getStyle()->getFont()->setBold(true);

        $conditionalStyles17 = $spreadsheet->getActiveSheet()->getStyle('D18')->getConditionalStyles();
        $conditionalStyles17[] = $conditional17;
        $spreadsheet->getActiveSheet()->getStyle('D18')->setConditionalStyles($conditionalStyles17);

        //Condition 18
        $conditional18 = new \PhpOffice\PhpSpreadsheet\Style\Conditional();
        $conditional18->setConditionType(\PhpOffice\PhpSpreadsheet\Style\Conditional::CONDITION_CELLIS);
        $conditional18->setOperatorType(\PhpOffice\PhpSpreadsheet\Style\Conditional::OPERATOR_LESSTHANOREQUAL);
        $conditional18->addCondition('E19');
        $conditional18->getStyle()->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_RED);
        $conditional18->getStyle()->getFont()->setBold(true);

        $conditionalStyles18 = $spreadsheet->getActiveSheet()->getStyle('D19')->getConditionalStyles();
        $conditionalStyles18[] = $conditional18;
        $spreadsheet->getActiveSheet()->getStyle('D19')->setConditionalStyles($conditionalStyles18);

        //Condition 19
        $conditional19 = new \PhpOffice\PhpSpreadsheet\Style\Conditional();
        $conditional19->setConditionType(\PhpOffice\PhpSpreadsheet\Style\Conditional::CONDITION_CELLIS);
        $conditional19->setOperatorType(\PhpOffice\PhpSpreadsheet\Style\Conditional::OPERATOR_LESSTHANOREQUAL);
        $conditional19->addCondition('E20');
        $conditional19->getStyle()->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_RED);
        $conditional19->getStyle()->getFont()->setBold(true);

        $conditionalStyles19 = $spreadsheet->getActiveSheet()->getStyle('D20')->getConditionalStyles();
        $conditionalStyles19[] = $conditional19;
        $spreadsheet->getActiveSheet()->getStyle('D20')->setConditionalStyles($conditionalStyles19);

        //Condition 20
        $conditional20 = new \PhpOffice\PhpSpreadsheet\Style\Conditional();
        $conditional20->setConditionType(\PhpOffice\PhpSpreadsheet\Style\Conditional::CONDITION_CELLIS);
        $conditional20->setOperatorType(\PhpOffice\PhpSpreadsheet\Style\Conditional::OPERATOR_NOTBETWEEN);
        $conditional20->addCondition('E21');
        $conditional20->addCondition('-E21');
        $conditional20->getStyle()->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_RED);
        $conditional20->getStyle()->getFont()->setBold(true);

        $conditionalStyles20 = $spreadsheet->getActiveSheet()->getStyle('D21')->getConditionalStyles();
        $conditionalStyles20[] = $conditional20;
        $spreadsheet->getActiveSheet()->getStyle('D21')->setConditionalStyles($conditionalStyles20);

        //Condition 21
        $conditional21 = new \PhpOffice\PhpSpreadsheet\Style\Conditional();
        $conditional21->setConditionType(\PhpOffice\PhpSpreadsheet\Style\Conditional::CONDITION_CELLIS);
        $conditional21->setOperatorType(\PhpOffice\PhpSpreadsheet\Style\Conditional::OPERATOR_NOTBETWEEN);
        $conditional21->addCondition('E22');
        $conditional21->addCondition('-E22');
        $conditional21->getStyle()->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_RED);
        $conditional21->getStyle()->getFont()->setBold(true);

        $conditionalStyles21 = $spreadsheet->getActiveSheet()->getStyle('D22')->getConditionalStyles();
        $conditionalStyles21[] = $conditional21;
        $spreadsheet->getActiveSheet()->getStyle('D22')->setConditionalStyles($conditionalStyles21);

        //Condition 22
        $conditional22 = new \PhpOffice\PhpSpreadsheet\Style\Conditional();
        $conditional22->setConditionType(\PhpOffice\PhpSpreadsheet\Style\Conditional::CONDITION_CELLIS);
        $conditional22->setOperatorType(\PhpOffice\PhpSpreadsheet\Style\Conditional::OPERATOR_BETWEEN);
        $conditional22->addCondition('E23');
        $conditional22->addCondition('-E23');
        $conditional22->getStyle()->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_RED);
        $conditional22->getStyle()->getFont()->setBold(true);

        $conditionalStyles22 = $spreadsheet->getActiveSheet()->getStyle('D23')->getConditionalStyles();
        $conditionalStyles22[] = $conditional22;
        $spreadsheet->getActiveSheet()->getStyle('D23')->setConditionalStyles($conditionalStyles22);

        //Condition 23
        $conditional23 = new \PhpOffice\PhpSpreadsheet\Style\Conditional();
        $conditional23->setConditionType(\PhpOffice\PhpSpreadsheet\Style\Conditional::CONDITION_CELLIS);
        $conditional23->setOperatorType(\PhpOffice\PhpSpreadsheet\Style\Conditional::OPERATOR_BETWEEN);
        $conditional23->addCondition('E24');
        $conditional23->addCondition('-E24');
        $conditional23->getStyle()->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_RED);
        $conditional23->getStyle()->getFont()->setBold(true);

        $conditionalStyles23 = $spreadsheet->getActiveSheet()->getStyle('D24')->getConditionalStyles();
        $conditionalStyles23[] = $conditional23;
        $spreadsheet->getActiveSheet()->getStyle('D24')->setConditionalStyles($conditionalStyles23);

        $conditionalFormattings = $worksheet->getConditionalStylesCollection();
        $calcer = Calculation::getInstance($spreadsheet);
        $calcer->disableCalculationCache();
        $precision = 8;

        // Taken from Excel 2113 manualy
        $should = [false, true, true, false, true, false, false, true, true, false, false, true, true, false, false, true, true, false, true, false, false, false, true, true];

        $actual = [];

        self::assertTrue(isset($conditionalFormattings));
        self::assertTrue(count($conditionalFormattings) > 0);

        foreach ($conditionalFormattings as $key => $formatings) {
            $split = explode(':', $key);
            $col = ord(substr($split[0], 0, 1));
            $multuseCol = false;

            if (count($split) > 1) {
                $colEnd = ord(substr($split[0], 0, 1));
            } else {
                $colEnd = $col;
            }

            $row = substr($split[0], 1);

            if (count($split) > 1) {
                $rowEnd = substr($split[1], 1);
            } else {
                $rowEnd = $row;
            }

            $multuseRow = false;
            if (isset($formatings) && count($formatings) > 0) {
                for ($i = $col; $i <= $colEnd; ++$i) {
                    for ($j = $row; $j <= $rowEnd; ++$j) {
                        foreach ($formatings as $formating) {
                            if ($col != $colEnd) {
                                $multuseCol = ($col - $i) * (-1);
                            }
                            if ($row != $rowEnd) {
                                $multuseRow = ($row - $j) * (-1);
                            }

                            $cell = $worksheet->getCell(chr($i) . $j);

                            $active = $formating->isActive($calcer, $cell, $precision, $multuseCol, $multuseRow);
                            $actual[$j - 1] = $active;
                        }
                    }
                }
            }
        }
        $count = count($should);
        for ($i = 0; $i < $count; ++$i) {
            self::assertEquals($should[$i], $actual[$i]);
        }
    }
}
