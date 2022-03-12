<?php

namespace PhpOffice\PhpSpreadsheetTests\Style;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpOffice\PhpSpreadsheet\Shared\File;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Conditional;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;
use PHPUnit\Framework\TestCase;

class ConditionalBoolTest extends TestCase
{
    /** @var string */
    private $outfile = '';

    protected function tearDown(): void
    {
        if ($this->outfile !== '') {
            unlink($this->outfile);
            $this->outfile = '';
        }
    }

    public function testBool(): void
    {
        $spreadsheet = new Spreadsheet();

        $sheet = $spreadsheet->getActiveSheet();
        $condition1 = new Conditional();
        $condition1->setConditionType(Conditional::CONDITION_CELLIS);
        $condition1->setOperatorType(Conditional::OPERATOR_EQUAL);
        $condition1->addCondition(false);
        $condition1->getStyle()->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getEndColor()->setARGB('FFFFFF00');
        $conditionalStyles = $sheet->getStyle('A1:A10')->getConditionalStyles();
        $conditionalStyles[] = $condition1;
        $sheet->getStyle('A1:A20')->setConditionalStyles($conditionalStyles);
        $sheet->setCellValue('A1', 1);
        $sheet->setCellValue('A2', true);
        $sheet->setCellValue('A3', false);
        $sheet->setCellValue('A4', 0.6);
        $sheet->setCellValue('A6', 0);
        $sheet->setSelectedCell('B1');

        $sheet = $spreadsheet->createSheet();
        $condition1 = new Conditional();
        $condition1->setConditionType(Conditional::CONDITION_CELLIS);
        $condition1->setOperatorType(Conditional::OPERATOR_EQUAL);
        $condition1->addCondition(true);
        $condition1->getStyle()->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getEndColor()->setARGB('FF00FF00');
        $conditionalStyles = $sheet->getStyle('A1:A10')->getConditionalStyles();
        $conditionalStyles[] = $condition1;
        $sheet->getStyle('A1:A20')->setConditionalStyles($conditionalStyles);
        $sheet->setCellValue('A1', 1);
        $sheet->setCellValue('A2', true);
        $sheet->setCellValue('A3', false);
        $sheet->setCellValue('A4', 0.6);
        $sheet->setCellValue('A6', 0);
        $sheet->setSelectedCell('B1');

        $writer = new XlsxWriter($spreadsheet);
        $this->outfile = File::temporaryFilename();
        $writer->save($this->outfile);
        $spreadsheet->disconnectWorksheets();

        $file = 'zip://' . $this->outfile . '#xl/worksheets/sheet1.xml';
        $contents = file_get_contents($file);
        self::assertNotFalse($contents);
        self::assertStringContainsString('<formula>FALSE</formula>', $contents);
        $file = 'zip://' . $this->outfile . '#xl/worksheets/sheet2.xml';
        $contents = file_get_contents($file);
        self::assertNotFalse($contents);
        self::assertStringContainsString('<formula>TRUE</formula>', $contents);

        $reader = new XlsxReader();
        $spreadsheet2 = $reader->load($this->outfile);
        $sheet1 = $spreadsheet2->getSheet(0);
        $condArray = $sheet1->getStyle('A1:A20')->getConditionalStyles();
        self::assertNotEmpty($condArray);
        $cond1 = $condArray[0];
        self::assertSame(Conditional::CONDITION_CELLIS, $cond1->getConditionType());
        self::assertSame(Conditional::OPERATOR_EQUAL, $cond1->getOperatorType());
        self::assertFalse(($cond1->getConditions())[0]);
        $sheet2 = $spreadsheet2->getSheet(1);
        $condArray = $sheet2->getStyle('A1:A20')->getConditionalStyles();
        self::assertNotEmpty($condArray);
        $cond1 = $condArray[0];
        self::assertSame(Conditional::CONDITION_CELLIS, $cond1->getConditionType());
        self::assertSame(Conditional::OPERATOR_EQUAL, $cond1->getOperatorType());
        self::assertTrue(($cond1->getConditions())[0]);
        $spreadsheet2->disconnectWorksheets();
    }
}
