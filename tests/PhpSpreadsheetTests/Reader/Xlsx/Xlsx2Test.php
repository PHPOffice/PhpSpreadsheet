<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\File;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Conditional;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PHPUnit\Framework\TestCase;

class Xlsx2Test extends TestCase
{
    public function testLoadXlsxConditionalFormatting2(): void
    {
        // Make sure Conditionals are read correctly from existing file
        $filename = 'tests/data/Reader/XLSX/conditionalFormatting2Test.xlsx';
        $reader = IOFactory::createReader('Xlsx');
        $spreadsheet = $reader->load($filename);
        $worksheet = $spreadsheet->getActiveSheet();

        $conditionalStyle = $worksheet->getConditionalStyles('A2:A8');
        self::assertNotEmpty($conditionalStyle);
        $conditionalRule = $conditionalStyle[0];
        $conditions = $conditionalRule->getConditions();
        self::assertNotEmpty($conditions);
        self::assertEquals(Conditional::CONDITION_NOTCONTAINSBLANKS, $conditionalRule->getConditionType());
        self::assertEquals('LEN(TRIM(A2))>0', $conditions[0]);

        $conditionalStyle = $worksheet->getConditionalStyles('B2:B8');
        self::assertNotEmpty($conditionalStyle);
        $conditionalRule = $conditionalStyle[0];
        $conditions = $conditionalRule->getConditions();
        self::assertNotEmpty($conditions);
        self::assertEquals(Conditional::CONDITION_CONTAINSBLANKS, $conditionalRule->getConditionType());
        self::assertEquals('LEN(TRIM(B2))=0', $conditions[0]);

        $conditionalStyle = $worksheet->getConditionalStyles('C2:C8');
        self::assertNotEmpty($conditionalStyle);
        $conditionalRule = $conditionalStyle[0];
        $conditions = $conditionalRule->getConditions();
        self::assertNotEmpty($conditions);
        self::assertEquals(Conditional::CONDITION_CELLIS, $conditionalRule->getConditionType());
        self::assertEquals(Conditional::OPERATOR_GREATERTHAN, $conditionalRule->getOperatorType());
        self::assertEquals('5', $conditions[0]);
    }

    public function testReloadXlsxConditionalFormatting2(): void
    {
        // Make sure conditionals from existing file are maintained across save
        $filename = 'tests/data/Reader/XLSX/conditionalFormatting2Test.xlsx';
        $outfile = File::temporaryFilename();
        $reader = IOFactory::createReader('Xlsx');
        $spreadshee1 = $reader->load($filename);
        $writer = IOFactory::createWriter($spreadshee1, 'Xlsx');
        $writer->save($outfile);
        $spreadsheet = $reader->load($outfile);
        unlink($outfile);
        $worksheet = $spreadsheet->getActiveSheet();

        $conditionalStyle = $worksheet->getConditionalStyles('A2:A8');
        self::assertNotEmpty($conditionalStyle);
        $conditionalRule = $conditionalStyle[0];
        $conditions = $conditionalRule->getConditions();
        self::assertNotEmpty($conditions);
        self::assertEquals(Conditional::CONDITION_NOTCONTAINSBLANKS, $conditionalRule->getConditionType());
        self::assertEquals('LEN(TRIM(A2))>0', $conditions[0]);

        $conditionalStyle = $worksheet->getConditionalStyles('B2:B8');
        self::assertNotEmpty($conditionalStyle);
        $conditionalRule = $conditionalStyle[0];
        $conditions = $conditionalRule->getConditions();
        self::assertNotEmpty($conditions);
        self::assertEquals(Conditional::CONDITION_CONTAINSBLANKS, $conditionalRule->getConditionType());
        self::assertEquals('LEN(TRIM(B2))=0', $conditions[0]);

        $conditionalStyle = $worksheet->getConditionalStyles('C2:C8');
        self::assertNotEmpty($conditionalStyle);
        $conditionalRule = $conditionalStyle[0];
        $conditions = $conditionalRule->getConditions();
        self::assertNotEmpty($conditions);
        self::assertEquals(Conditional::CONDITION_CELLIS, $conditionalRule->getConditionType());
        self::assertEquals(Conditional::OPERATOR_GREATERTHAN, $conditionalRule->getOperatorType());
        self::assertEquals('5', $conditions[0]);
    }

    public function testNewXlsxConditionalFormatting2(): void
    {
        // Make sure blanks/non-blanks added by PhpSpreadsheet are handled correctly
        $outfile = File::temporaryFilename();
        $spreadshee1 = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadshee1->getActiveSheet();
        $sheet->setCellValue('A2', 'a2');
        $sheet->setCellValue('A4', 'a4');
        $sheet->setCellValue('A6', 'a6');
        $cond1 = new Conditional();
        $cond1->setConditionType(Conditional::CONDITION_CONTAINSBLANKS);
        $cond1->getStyle()->getFill()->setFillType(Fill::FILL_SOLID);
        $cond1->getStyle()->getFill()->getStartColor()->setARGB(Color::COLOR_RED);
        $cond = [$cond1];
        $sheet->getStyle('A1:A6')->setConditionalStyles($cond);
        $writer = IOFactory::createWriter($spreadshee1, 'Xlsx');
        $writer->save($outfile);
        $reader = IOFactory::createReader('Xlsx');
        $spreadsheet = $reader->load($outfile);
        unlink($outfile);
        $worksheet = $spreadsheet->getActiveSheet();

        $conditionalStyle = $worksheet->getConditionalStyles('A1:A6');
        self::assertNotEmpty($conditionalStyle);
        $conditionalRule = $conditionalStyle[0];
        $conditions = $conditionalRule->getConditions();
        self::assertNotEmpty($conditions);
        self::assertEquals(Conditional::CONDITION_CONTAINSBLANKS, $conditionalRule->getConditionType());
        self::assertEquals('LEN(TRIM(A1))=0', $conditions[0]);
    }
}
