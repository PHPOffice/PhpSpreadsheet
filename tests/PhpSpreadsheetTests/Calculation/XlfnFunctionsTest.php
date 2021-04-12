<?php

namespace PhpOffice\PhpSpreadsheetTests\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Shared\File;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Conditional;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class XlfnFunctionsTest extends \PHPUnit\Framework\TestCase
{
    public function testXlfn(): void
    {
        $formulas = [
            // null indicates function not implemented in Calculation engine
            ['2010', 'A1', '=MODE.SNGL({5.6,4,4,3,2,4})', '=_xlfn.MODE.SNGL({5.6,4,4,3,2,4})', 4],
            ['2010', 'A2', '=MODE.SNGL({"x","y"})', '=_xlfn.MODE.SNGL({"x","y"})', '#N/A'],
            ['2013', 'A1', '=ISOWEEKNUM("2019-12-19")', '=_xlfn.ISOWEEKNUM("2019-12-19")', 51],
            ['2013', 'A2', '=SHEET("2019")', '=_xlfn.SHEET("2019")', null],
            ['2013', 'A3', '2019-01-04', '2019-01-04', null],
            ['2013', 'A4', '2019-07-04', '2019-07-04', null],
            ['2013', 'A5', '2019-12-04', '2019-12-04', null],
            ['2013', 'B3', 1, 1, null],
            ['2013', 'B4', 2, 2, null],
            ['2013', 'B5', -3, -3, null],
            // multiple xlfn functions interleaved with non-xlfn
            ['2013', 'C3', '=ISOWEEKNUM(A3)+WEEKNUM(A4)+ISOWEEKNUM(A5)', '=_xlfn.ISOWEEKNUM(A3)+WEEKNUM(A4)+_xlfn.ISOWEEKNUM(A5)', 77],
            ['2016', 'A1', '=SWITCH(WEEKDAY("2019-12-22",1),1,"Sunday",2,"Monday","No Match")', '=_xlfn.SWITCH(WEEKDAY("2019-12-22",1),1,"Sunday",2,"Monday","No Match")', 'Sunday'],
            ['2016', 'B1', '=SWITCH(WEEKDAY("2019-12-20",1),1,"Sunday",2,"Monday","No Match")', '=_xlfn.SWITCH(WEEKDAY("2019-12-20",1),1,"Sunday",2,"Monday","No Match")', 'No Match'],
            ['2019', 'A1', '=CONCAT("The"," ","sun"," ","will"," ","come"," ","up"," ","tomorrow.")', '=_xlfn.CONCAT("The"," ","sun"," ","will"," ","come"," ","up"," ","tomorrow.")', 'The sun will come up tomorrow.'],
        ];
        $workbook = new Spreadsheet();
        $sheet = $workbook->getActiveSheet();
        $sheet->setTitle('2010');
        $sheet = $workbook->createSheet();
        $sheet->setTitle('2013');
        $sheet = $workbook->createSheet();
        $sheet->setTitle('2016');
        $sheet = $workbook->createSheet();
        $sheet->setTitle('2019');

        foreach ($formulas as $values) {
            $sheet = $workbook->setActiveSheetIndexByName($values[0]);
            $sheet->setCellValue($values[1], $values[2]);
        }

        $sheet = $workbook->setActiveSheetIndexByName('2013');
        $sheet->getStyle('A3:A5')->getNumberFormat()->setFormatCode('yyyy-mm-dd');
        $sheet->getColumnDimension('A')->setAutoSize(true);
        $condition0 = new Conditional();
        $condition0->setConditionType(Conditional::CONDITION_EXPRESSION);
        $condition0->addCondition('ABS(B3)<2');
        $condition0->getStyle()->getFill()->setFillType(Fill::FILL_SOLID);
        $condition0->getStyle()->getFill()->getEndColor()->setARGB(Color::COLOR_RED);
        $condition1 = new Conditional();
        $condition1->setConditionType(Conditional::CONDITION_EXPRESSION);
        $condition1->addCondition('ABS(B3)>2');
        $condition1->getStyle()->getFill()->setFillType(Fill::FILL_SOLID);
        $condition1->getStyle()->getFill()->getEndColor()->setARGB(Color::COLOR_GREEN);
        $cond = [$condition0, $condition1];
        $sheet->getStyle('B3:B5')->setConditionalStyles($cond);
        $condition0 = new Conditional();
        $condition0->setConditionType(Conditional::CONDITION_EXPRESSION);
        $condition0->addCondition('ISOWEEKNUM(A3)<10');
        $condition0->getStyle()->getFill()->setFillType(Fill::FILL_SOLID);
        $condition0->getStyle()->getFill()->getEndColor()->setARGB(Color::COLOR_RED);
        $condition1 = new Conditional();
        $condition1->setConditionType(Conditional::CONDITION_EXPRESSION);
        $condition1->addCondition('ISOWEEKNUM(A3)>40');
        $condition1->getStyle()->getFill()->setFillType(Fill::FILL_SOLID);
        $condition1->getStyle()->getFill()->getEndColor()->setARGB(Color::COLOR_GREEN);
        $cond = [$condition0, $condition1];
        $sheet->getStyle('A3:A5')->setConditionalStyles($cond);
        $sheet->setSelectedCell('B1');

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($workbook, 'Xlsx');
        $oufil = File::temporaryFilename();
        $writer->save($oufil);

        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');
        $rdobj = $reader->load($oufil);
        unlink($oufil);
        foreach ($formulas as $values) {
            $sheet = $rdobj->setActiveSheetIndexByName($values[0]);
            self::assertEquals($values[3], $sheet->getCell($values[1])->getValue());
            if ($values[4] !== null) {
                self::assertEquals($values[4], $sheet->getCell($values[1])->getCalculatedValue());
            }
        }
        $sheet = $rdobj->setActiveSheetIndexByName('2013');
        $cond = $sheet->getConditionalStyles('A3:A5');
        self::assertEquals('_xlfn.ISOWEEKNUM(A3)<10', $cond[0]->getConditions()[0]);
        self::assertEquals('_xlfn.ISOWEEKNUM(A3)>40', $cond[1]->getConditions()[0]);
        $cond = $sheet->getConditionalStyles('B3:B5');
        self::assertEquals('ABS(B3)<2', $cond[0]->getConditions()[0]);
        self::assertEquals('ABS(B3)>2', $cond[1]->getConditions()[0]);
    }
}
