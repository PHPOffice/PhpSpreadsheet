<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation;

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
            ['2010', 'A1', '=MODE.SNGL({5.6,4,4,3,2,4})', '=MODE.SNGL({5.6,4,4,3,2,4})', 4],
            ['2010', 'A2', '=MODE.SNGL({"x","y"})', '=MODE.SNGL({"x","y"})', '#N/A'],
            ['2013', 'A1', '=ISOWEEKNUM("2019-12-19")', '=ISOWEEKNUM("2019-12-19")', 51],
            ['2013', 'A2', '=SHEET("2019")', '=SHEET("2019")', null],
            ['2013', 'A3', '2019-01-04', '2019-01-04', null],
            ['2013', 'A4', '2019-07-04', '2019-07-04', null],
            ['2013', 'A5', '2019-12-04', '2019-12-04', null],
            ['2013', 'B3', 1, 1, null],
            ['2013', 'B4', 2, 2, null],
            ['2013', 'B5', -3, -3, null],
            // multiple xlfn functions interleaved with non-xlfn
            ['2013', 'C3', '=ISOWEEKNUM(A3)+WEEKNUM(A4)+ISOWEEKNUM(A5)', '=ISOWEEKNUM(A3)+WEEKNUM(A4)+ISOWEEKNUM(A5)', 77],
            ['2016', 'A1', '=SWITCH(WEEKDAY("2019-12-22",1),1,"Sunday",2,"Monday","No Match")', '=SWITCH(WEEKDAY("2019-12-22",1),1,"Sunday",2,"Monday","No Match")', 'Sunday'],
            ['2016', 'B1', '=SWITCH(WEEKDAY("2019-12-20",1),1,"Sunday",2,"Monday","No Match")', '=SWITCH(WEEKDAY("2019-12-20",1),1,"Sunday",2,"Monday","No Match")', 'No Match'],
            ['2019', 'A1', '=CONCAT("The"," ","sun"," ","will"," ","come"," ","up"," ","tomorrow.")', '=CONCAT("The"," ","sun"," ","will"," ","come"," ","up"," ","tomorrow.")', 'The sun will come up tomorrow.'],
            ['365', 'A1', '=SORT({7;1;5})', '=SORT({7;1;5})', 1],
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
        $sheet = $workbook->createSheet();
        $sheet->setTitle('365');

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
        $condition0->getStyle()->getFill()->getStartColor()->setARGB(Color::COLOR_RED);
        $condition1 = new Conditional();
        $condition1->setConditionType(Conditional::CONDITION_EXPRESSION);
        $condition1->addCondition('ABS(B3)>2');
        $condition1->getStyle()->getFill()->setFillType(Fill::FILL_SOLID);
        $condition1->getStyle()->getFill()->getStartColor()->setARGB(Color::COLOR_GREEN);
        $cond = [$condition0, $condition1];
        $sheet->getStyle('B3:B5')->setConditionalStyles($cond);
        $condition0 = new Conditional();
        $condition0->setConditionType(Conditional::CONDITION_EXPRESSION);
        $condition0->addCondition('ISOWEEKNUM(A3)<10');
        $condition0->getStyle()->getFill()->setFillType(Fill::FILL_SOLID);
        $condition0->getStyle()->getFill()->getStartColor()->setARGB(Color::COLOR_RED);
        $condition1 = new Conditional();
        $condition1->setConditionType(Conditional::CONDITION_EXPRESSION);
        $condition1->addCondition('ISOWEEKNUM(A3)>40');
        $condition1->getStyle()->getFill()->setFillType(Fill::FILL_SOLID);
        $condition1->getStyle()->getFill()->getStartColor()->setARGB(Color::COLOR_GREEN);
        $cond = [$condition0, $condition1];
        $sheet->getStyle('A3:A5')->setConditionalStyles($cond);
        $sheet->setSelectedCell('B1');

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($workbook, 'Xlsx');
        $oufil = File::temporaryFilename();
        $writer->save($oufil);

        $file = "zip://$oufil#xl/worksheets/sheet1.xml";
        $contents = (string) file_get_contents($file);
        self::assertStringContainsString('<c r="A1"><f>_xlfn.MODE.SNGL({5.6,4,4,3,2,4})</f><v>4</v></c>', $contents);
        self::assertStringContainsString('<c r="A2" t="e"><f>_xlfn.MODE.SNGL({&quot;x&quot;,&quot;y&quot;})</f><v>#N/A</v></c>', $contents);

        $file = "zip://$oufil#xl/worksheets/sheet2.xml";
        $contents = (string) file_get_contents($file);
        self::assertStringContainsString('<c r="A1"><f>_xlfn.ISOWEEKNUM(&quot;2019-12-19&quot;)</f><v>51</v></c>', $contents);
        self::assertStringContainsString('<c r="A2"><f>_xlfn.SHEET(&quot;2019&quot;)</f></c>', $contents);
        self::assertStringContainsString('<c r="C3"><f>_xlfn.ISOWEEKNUM(A3)+WEEKNUM(A4)+_xlfn.ISOWEEKNUM(A5)</f><v>77</v></c>', $contents);
        self::assertStringContainsString('<conditionalFormatting sqref="B3:B5"><cfRule type="expression" dxfId="0" priority="1"><formula>ABS(B3)&lt;2</formula></cfRule><cfRule type="expression" dxfId="1" priority="2"><formula>ABS(B3)&gt;2</formula></cfRule></conditionalFormatting>', $contents);
        self::assertStringContainsString('<conditionalFormatting sqref="A3:A5"><cfRule type="expression" dxfId="2" priority="3"><formula>_xlfn.ISOWEEKNUM(A3)&lt;10</formula></cfRule><cfRule type="expression" dxfId="3" priority="4"><formula>_xlfn.ISOWEEKNUM(A3)&gt;40</formula></cfRule></conditionalFormatting>', $contents);

        $file = "zip://$oufil#xl/worksheets/sheet3.xml";
        $contents = (string) file_get_contents($file);
        self::assertStringContainsString('<c r="A1" t="str"><f>_xlfn.SWITCH(WEEKDAY(&quot;2019-12-22&quot;,1),1,&quot;Sunday&quot;,2,&quot;Monday&quot;,&quot;No Match&quot;)</f><v>Sunday</v></c>', $contents);
        self::assertStringContainsString('<c r="B1" t="str"><f>_xlfn.SWITCH(WEEKDAY(&quot;2019-12-20&quot;,1),1,&quot;Sunday&quot;,2,&quot;Monday&quot;,&quot;No Match&quot;)</f><v>No Match</v></c>', $contents);

        $file = "zip://$oufil#xl/worksheets/sheet4.xml";
        $contents = (string) file_get_contents($file);
        self::assertStringContainsString('<c r="A1" t="str"><f>_xlfn.CONCAT(&quot;The&quot;,&quot; &quot;,&quot;sun&quot;,&quot; &quot;,&quot;will&quot;,&quot; &quot;,&quot;come&quot;,&quot; &quot;,&quot;up&quot;,&quot; &quot;,&quot;tomorrow.&quot;)</f><v>The sun will come up tomorrow.</v></c>', $contents);

        $file = "zip://$oufil#xl/worksheets/sheet5.xml";
        $contents = (string) file_get_contents($file);
        self::assertStringContainsString('<c r="A1"><f>_xlfn._xlws.SORT({7;1;5})</f><v>1</v></c>', $contents);

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
        self::assertEquals('ISOWEEKNUM(A3)<10', $cond[0]->getConditions()[0]);
        self::assertEquals('ISOWEEKNUM(A3)>40', $cond[1]->getConditions()[0]);
        $cond = $sheet->getConditionalStyles('B3:B5');
        self::assertEquals('ABS(B3)<2', $cond[0]->getConditions()[0]);
        self::assertEquals('ABS(B3)>2', $cond[1]->getConditions()[0]);
    }
}
