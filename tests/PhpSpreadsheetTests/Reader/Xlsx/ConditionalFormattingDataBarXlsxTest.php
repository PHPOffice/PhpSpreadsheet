<?php

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\File;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Conditional;
use PhpOffice\PhpSpreadsheet\Style\ConditionalFormatting\ConditionalDataBar;
use PhpOffice\PhpSpreadsheet\Style\ConditionalFormatting\ConditionalFormatValueObject;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PHPUnit\Framework\TestCase;

class ConditionalFormattingDataBarXlsxTest extends TestCase
{
    public function testLoadXlsxConditionalFormattingDataBar(): void
    {
        // Make sure Conditionals are read correctly from existing file
        $filename = 'tests/data/Reader/XLSX/conditionalFormattingDataBarTest.xlsx';
        $reader = IOFactory::createReader('Xlsx');
        $spreadsheet = $reader->load($filename);
        $worksheet = $spreadsheet->getActiveSheet();

        $this->pattern1Assertion($worksheet);
        $this->pattern2Assertion($worksheet);
        $this->pattern3Assertion($worksheet);
        $this->pattern4Assertion($worksheet);
    }

    public function testReloadXlsxConditionalFormattingDataBar(): void
    {
        // Make sure conditionals from existing file are maintained across save
        $filename = 'tests/data/Reader/XLSX/conditionalFormattingDataBarTest.xlsx';
        $outfile = File::temporaryFilename();
        $reader = IOFactory::createReader('Xlsx');
        $spreadshee1 = $reader->load($filename);
        $writer = IOFactory::createWriter($spreadshee1, 'Xlsx');
        $writer->save($outfile);
        $spreadsheet = $reader->load($outfile);
        unlink($outfile);
        $worksheet = $spreadsheet->getActiveSheet();

        $this->pattern1Assertion($worksheet);
        $this->pattern2Assertion($worksheet);
        $this->pattern3Assertion($worksheet);
        $this->pattern4Assertion($worksheet);
    }

    public function testNewXlsxConditionalFormattingDataBar(): void
    {
        // Make sure blanks/non-blanks added by PhpSpreadsheet are handled correctly
        $outfile = File::temporaryFilename();
        $spreadshee1 = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadshee1->getActiveSheet();
        $sheet->setCellValue('A1', 1);
        $sheet->setCellValue('A2', 2);
        $sheet->setCellValue('A3', 3);
        $sheet->setCellValue('A4', 4);
        $sheet->setCellValue('A5', 5);
        $cond1 = new Conditional();
        $cond1->setConditionType(Conditional::CONDITION_DATABAR);
        $cond1->setDataBar(new ConditionalDataBar());
        $dataBar = $cond1->getDataBar();
        self::assertNotNull($dataBar);
        $dataBar
            ->setMinimumConditionalFormatValueObject(new ConditionalFormatValueObject('min'))
            ->setMaximumConditionalFormatValueObject(new ConditionalFormatValueObject('max'))
            ->setColor(Color::COLOR_GREEN);
        $cond = [$cond1];
        $sheet->getStyle('A1:A5')->setConditionalStyles($cond);
        $writer = IOFactory::createWriter($spreadshee1, 'Xlsx');
        $writer->save($outfile);
        $reader = IOFactory::createReader('Xlsx');
        $spreadsheet = $reader->load($outfile);
        unlink($outfile);
        $worksheet = $spreadsheet->getActiveSheet();

        $conditionalStyle = $worksheet->getConditionalStyles('A1:A5');
        self::assertNotEmpty($conditionalStyle);
        /** @var Conditional $conditionalRule */
        $conditionalRule = $conditionalStyle[0];
        $conditions = $conditionalRule->getConditions();
        self::assertNotEmpty($conditions);
        self::assertEquals(Conditional::CONDITION_DATABAR, $conditionalRule->getConditionType());
        self::assertNotEmpty($conditionalRule->getDataBar());

        $dataBar = $conditionalRule->getDataBar();
        self::assertNotNull($dataBar);
        self::assertNotNull($dataBar->getMinimumConditionalFormatValueObject());
        self::assertNotNull($dataBar->getMaximumConditionalFormatValueObject());
        self::assertEquals('min', $dataBar->getMinimumConditionalFormatValueObject()->getType());
        self::assertEquals('max', $dataBar->getMaximumConditionalFormatValueObject()->getType());
        self::assertEquals(Color::COLOR_GREEN, $dataBar->getColor());
    }

    private function pattern1Assertion(Worksheet $worksheet): void
    {
        self::assertEquals(
            "Type: Automatic, Automatic\nDirection: Automatic\nFills: Gradient\nAxis Position: Automatic",
            $worksheet->getCell('A2')->getValue()
        );

        $conditionalStyle = $worksheet->getConditionalStyles('A3:A23');
        self::assertNotEmpty($conditionalStyle);
        /** @var Conditional $conditionalRule */
        $conditionalRule = $conditionalStyle[0];
        $dataBar = $conditionalRule->getDataBar();

        self::assertNotEmpty($dataBar);
        self::assertEquals(Conditional::CONDITION_DATABAR, $conditionalRule->getConditionType());
        self::assertNotNull($dataBar);
        self::assertNotNull($dataBar->getMinimumConditionalFormatValueObject());
        self::assertNotNull($dataBar->getMaximumConditionalFormatValueObject());
        self::assertEquals('min', $dataBar->getMinimumConditionalFormatValueObject()->getType());
        self::assertEquals('max', $dataBar->getMaximumConditionalFormatValueObject()->getType());

        self::assertEquals('FF638EC6', $dataBar->getColor());
        self::assertNotEmpty($dataBar->getConditionalFormattingRuleExt());
        //ext
        $rule1ext = $dataBar->getConditionalFormattingRuleExt();
        self::assertNotNull($rule1ext);
        self::assertEquals('{72C64AE0-5CD9-164F-83D1-AB720F263E79}', $rule1ext->getId());
        self::assertEquals('dataBar', $rule1ext->getCfRule());
        self::assertEquals('A3:A23', $rule1ext->getSqref());
        $extDataBar = $rule1ext->getDataBarExt();
        self::assertNotEmpty($extDataBar);
        $pattern1 = [
            'minLength' => 0,
            'maxLength' => 100,
            'border' => true,
            'gradient' => null,
            'direction' => null,
            'axisPosition' => null,
            'negativeBarBorderColorSameAsPositive' => false,
            'borderColor' => 'FF638EC6',
            'negativeFillColor' => 'FFFF0000',
            'negativeBorderColor' => 'FFFF0000',
        ];
        foreach ($pattern1 as $key => $value) {
            $funcName = 'get' . ucwords($key);
            self::assertEquals($value, $extDataBar->$funcName(), __METHOD__ . '::' . $funcName . ' function patten');
        }

        self::assertNotEmpty($extDataBar->getMinimumConditionalFormatValueObject());
        self::assertNotEmpty($extDataBar->getMaximumConditionalFormatValueObject());
        self::assertEquals('autoMin', $extDataBar->getMinimumConditionalFormatValueObject()->getType());
        self::assertEquals('autoMax', $extDataBar->getMaximumConditionalFormatValueObject()->getType());

        self::assertArrayHasKey('rgb', $extDataBar->getAxisColor());
        self::assertEquals('FF000000', $extDataBar->getAxisColor()['rgb']);
    }

    private function pattern2Assertion(Worksheet $worksheet): void
    {
        self::assertEquals(
            "Type: Number, Number\nValue: -5, 5\nDirection: Automatic\nFills: Solid\nAxis Position: Automatic",
            $worksheet->getCell('B2')->getValue()
        );

        $conditionalStyle = $worksheet->getConditionalStyles('B3:B23');
        self::assertNotEmpty($conditionalStyle);
        /** @var Conditional $conditionalRule */
        $conditionalRule = $conditionalStyle[0];
        $dataBar = $conditionalRule->getDataBar();

        self::assertNotEmpty($dataBar);
        self::assertEquals(Conditional::CONDITION_DATABAR, $conditionalRule->getConditionType());
        self::assertNotNull($dataBar);
        self::assertNotNull($dataBar->getMinimumConditionalFormatValueObject());
        self::assertNotNull($dataBar->getMaximumConditionalFormatValueObject());
        self::assertEquals('num', $dataBar->getMinimumConditionalFormatValueObject()->getType());
        self::assertEquals('num', $dataBar->getMaximumConditionalFormatValueObject()->getType());
        self::assertEquals('-5', $dataBar->getMinimumConditionalFormatValueObject()->getValue());
        self::assertEquals('5', $dataBar->getMaximumConditionalFormatValueObject()->getValue());
        self::assertEquals('FF63C384', $dataBar->getColor());
        self::assertNotEmpty($dataBar->getConditionalFormattingRuleExt());
        //ext
        $rule1ext = $dataBar->getConditionalFormattingRuleExt();
        self::assertNotNull($rule1ext);
        self::assertEquals('{98904F60-57F0-DF47-B480-691B20D325E3}', $rule1ext->getId());
        self::assertEquals('dataBar', $rule1ext->getCfRule());
        self::assertEquals('B3:B23', $rule1ext->getSqref());
        $extDataBar = $rule1ext->getDataBarExt();
        self::assertNotEmpty($extDataBar);
        $pattern1 = [
            'minLength' => 0,
            'maxLength' => 100,
            'border' => null,
            'gradient' => false,
            'direction' => null,
            'axisPosition' => null,
            'negativeBarBorderColorSameAsPositive' => null,
            'borderColor' => null,
            'negativeFillColor' => 'FFFF0000',
            'negativeBorderColor' => null,
        ];
        foreach ($pattern1 as $key => $value) {
            $funcName = 'get' . ucwords($key);
            self::assertEquals($value, $extDataBar->$funcName(), $funcName . ' function patten');
        }

        self::assertNotEmpty($extDataBar->getMinimumConditionalFormatValueObject());
        self::assertNotEmpty($extDataBar->getMaximumConditionalFormatValueObject());
        self::assertEquals('num', $extDataBar->getMinimumConditionalFormatValueObject()->getType());
        self::assertEquals('num', $extDataBar->getMaximumConditionalFormatValueObject()->getType());
        self::assertEquals('-5', $extDataBar->getMinimumConditionalFormatValueObject()->getCellFormula());
        self::assertEquals('5', $extDataBar->getMaximumConditionalFormatValueObject()->getCellFormula());

        self::assertArrayHasKey('rgb', $extDataBar->getAxisColor());
        self::assertEquals('FF000000', $extDataBar->getAxisColor()['rgb']);
    }

    private function pattern3Assertion(Worksheet $worksheet): void
    {
        self::assertEquals(
            "Type: Automatic, Automatic\nDirection: rightToLeft\nFills: Solid\nAxis Position: None",
            $worksheet->getCell('C2')->getValue()
        );

        $conditionalStyle = $worksheet->getConditionalStyles('C3:C23');
        self::assertNotEmpty($conditionalStyle);
        /** @var Conditional $conditionalRule */
        $conditionalRule = $conditionalStyle[0];
        $dataBar = $conditionalRule->getDataBar();

        self::assertNotEmpty($dataBar);
        self::assertEquals(Conditional::CONDITION_DATABAR, $conditionalRule->getConditionType());
        self::assertNotNull($dataBar);
        self::assertNotNull($dataBar->getMinimumConditionalFormatValueObject());
        self::assertNotNull($dataBar->getMaximumConditionalFormatValueObject());
        self::assertEquals('min', $dataBar->getMinimumConditionalFormatValueObject()->getType());
        self::assertEquals('max', $dataBar->getMaximumConditionalFormatValueObject()->getType());
        self::assertEmpty($dataBar->getMinimumConditionalFormatValueObject()->getValue());
        self::assertEmpty($dataBar->getMaximumConditionalFormatValueObject()->getValue());
        self::assertEquals('FFFF555A', $dataBar->getColor());
        self::assertNotEmpty($dataBar->getConditionalFormattingRuleExt());

        //ext
        $rule1ext = $dataBar->getConditionalFormattingRuleExt();
        self::assertNotNull($rule1ext);
        self::assertEquals('{453C04BA-7ABD-8548-8A17-D9CFD2BDABE9}', $rule1ext->getId());
        self::assertEquals('dataBar', $rule1ext->getCfRule());
        self::assertEquals('C3:C23', $rule1ext->getSqref());
        $extDataBar = $rule1ext->getDataBarExt();
        self::assertNotEmpty($extDataBar);
        $pattern1 = [
            'minLength' => 0,
            'maxLength' => 100,
            'border' => null,
            'gradient' => false,
            'direction' => 'rightToLeft',
            'axisPosition' => 'none',
            'negativeBarBorderColorSameAsPositive' => null,
            'borderColor' => null,
            'negativeFillColor' => 'FFFF0000',
            'negativeBorderColor' => null,
        ];
        foreach ($pattern1 as $key => $value) {
            $funcName = 'get' . ucwords($key);
            self::assertEquals($value, $extDataBar->$funcName(), $funcName . ' function patten');
        }

        self::assertNotEmpty($extDataBar->getMinimumConditionalFormatValueObject());
        self::assertNotEmpty($extDataBar->getMaximumConditionalFormatValueObject());
        self::assertEquals('autoMin', $extDataBar->getMinimumConditionalFormatValueObject()->getType());
        self::assertEquals('autoMax', $extDataBar->getMaximumConditionalFormatValueObject()->getType());
        self::assertEmpty($extDataBar->getMinimumConditionalFormatValueObject()->getCellFormula());
        self::assertEmpty($extDataBar->getMaximumConditionalFormatValueObject()->getCellFormula());

        self::assertArrayHasKey('rgb', $extDataBar->getAxisColor());
        self::assertEmpty($extDataBar->getAxisColor()['rgb']);
    }

    private function pattern4Assertion(Worksheet $worksheet): void
    {
        self::assertEquals(
            "type: formula, formula\nValue: =2+3, =10+10\nDirection: leftToRight\nShowDataBarOnly\nFills: Solid\nBorder: Solid\nAxis Position: Midpoint",
            $worksheet->getCell('D2')->getValue()
        );

        $conditionalStyle = $worksheet->getConditionalStyles('D3:D23');
        self::assertNotEmpty($conditionalStyle);
        /** @var Conditional $conditionalRule */
        $conditionalRule = $conditionalStyle[0];
        $dataBar = $conditionalRule->getDataBar();

        self::assertNotEmpty($dataBar);
        self::assertEquals(Conditional::CONDITION_DATABAR, $conditionalRule->getConditionType());

        self::assertNotNull($dataBar);
        self::assertTrue($dataBar->getShowValue());
        self::assertNotNull($dataBar->getMinimumConditionalFormatValueObject());
        self::assertNotNull($dataBar->getMaximumConditionalFormatValueObject());
        self::assertEquals('formula', $dataBar->getMinimumConditionalFormatValueObject()->getType());
        self::assertEquals('formula', $dataBar->getMaximumConditionalFormatValueObject()->getType());
        self::assertEquals('3+2', $dataBar->getMinimumConditionalFormatValueObject()->getValue());
        self::assertEquals('10+10', $dataBar->getMaximumConditionalFormatValueObject()->getValue());
        self::assertEquals('FFFF555A', $dataBar->getColor());
        self::assertNotEmpty($dataBar->getConditionalFormattingRuleExt());

        //ext
        $rule1ext = $dataBar->getConditionalFormattingRuleExt();
        self::assertNotNull($rule1ext);
        self::assertEquals('{6C1E066A-E240-3D4A-98F8-8CC218B0DFD2}', $rule1ext->getId());
        self::assertEquals('dataBar', $rule1ext->getCfRule());
        self::assertEquals('D3:D23', $rule1ext->getSqref());
        $extDataBar = $rule1ext->getDataBarExt();
        self::assertNotEmpty($extDataBar);
        $pattern1 = [
            'minLength' => 0,
            'maxLength' => 100,
            'border' => true,
            'gradient' => false,
            'direction' => 'leftToRight',
            'axisPosition' => 'middle',
            'negativeBarBorderColorSameAsPositive' => null,
            'borderColor' => 'FF000000',
            'negativeFillColor' => 'FFFF0000',
            'negativeBorderColor' => null,
        ];
        foreach ($pattern1 as $key => $value) {
            $funcName = 'get' . ucwords($key);
            self::assertEquals($value, $extDataBar->$funcName(), $funcName . ' function patten');
        }

        self::assertNotEmpty($extDataBar->getMaximumConditionalFormatValueObject());
        self::assertNotEmpty($extDataBar->getMinimumConditionalFormatValueObject());
        self::assertEquals('formula', $extDataBar->getMinimumConditionalFormatValueObject()->getType());
        self::assertEquals('formula', $extDataBar->getMaximumConditionalFormatValueObject()->getType());
        self::assertEquals('3+2', $extDataBar->getMinimumConditionalFormatValueObject()->getCellFormula());
        self::assertEquals('10+10', $extDataBar->getMaximumConditionalFormatValueObject()->getCellFormula());

        self::assertArrayHasKey('rgb', $extDataBar->getAxisColor());
        self::assertEquals('FF000000', $extDataBar->getAxisColor()['rgb']);
    }
}
