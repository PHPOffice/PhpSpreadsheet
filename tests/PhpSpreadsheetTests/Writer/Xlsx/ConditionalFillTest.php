<?php

namespace PhpOffice\PhpSpreadsheetTests\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Shared\File;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Conditional;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;
use PHPUnit\Framework\TestCase;

class ConditionalFillTest extends TestCase
{
    public function testFill(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getCell('A1')->setValue(10);
        $sheet->getCell('A2')->setValue(20);
        $sheet->getCell('A3')->setValue(30);
        $sheet->getCell('A4')->setValue(40);

        $sheet->getStyle('A1')->getFill()->setFillType(Fill::FILL_SOLID);
        $sheet->getStyle('A1')->getFill()->getStartColor()->setARGB('FFFF0000');
        $sheet->getStyle('A2')->getFill()->setFillType(Fill::FILL_SOLID);
        // Need to specify StartColor for desired effect
        $sheet->getStyle('A2')->getFill()->getEndColor()->setARGB('FF00FF00');

        $conditional1 = new Conditional();
        $conditional1->setConditionType(Conditional::CONDITION_CELLIS);
        $conditional1->setOperatorType(Conditional::OPERATOR_GREATERTHAN);
        $conditional1->addCondition(35);
        $conditional1->getStyle()->getFill()->setFillType(Fill::FILL_SOLID);
        $conditional1->getStyle()->getFill()->getEndColor()->setARGB('FF0000FF');

        $conditional2 = new Conditional();
        $conditional2->setConditionType(Conditional::CONDITION_CELLIS);
        $conditional2->setOperatorType(Conditional::OPERATOR_LESSTHAN);
        $conditional2->addCondition(35);
        $conditional2->getStyle()->getFill()->setFillType(Fill::FILL_SOLID);
        // Before issue37303202, had needed to specify EndColor for desired effect.
        // This was the opposite of non-Conditional style.
        $conditional2->getStyle()->getFill()->getStartColor()->setARGB('FFFFFF00');

        $conditionalStyles = $spreadsheet->getActiveSheet()->getStyle('A3:A4')->getConditionalStyles();
        $conditionalStyles[] = $conditional1;
        $conditionalStyles[] = $conditional2;

        $spreadsheet->getActiveSheet()->getStyle('A3:A4')->setConditionalStyles($conditionalStyles);
        $sheet->setSelectedCells('C1');

        $outfile = File::temporaryFilename();
        $writer = new XlsxWriter($spreadsheet);
        $writer->save($outfile);
        $spreadsheet->disconnectWorksheets();

        $file = 'zip://';
        $file .= $outfile;
        $file .= '#xl/styles.xml';
        $data = file_get_contents($file);
        unlink($outfile);

        $expected = '<fill>'
            . '<patternFill patternType="solid">'
            . '<fgColor rgb="FFFF0000"/>'
            . '<bgColor rgb="FF000000"/>'
            . '</patternFill>'
            . '</fill>';
        self::assertStringContainsString($expected, $data, 'style for A1');
        $expected = '<fill>'
            . '<patternFill patternType="solid">'
            . '<fgColor rgb="FFFFFFFF"/>'
            . '<bgColor rgb="FF00FF00"/>'
            . '</patternFill>'
            . '</fill>';
        self::assertStringContainsString($expected, $data, 'style for A2');
        $expected = '<dxf><fill>'
            . '<patternFill patternType="solid">'
            . '<bgColor rgb="FF0000FF"/>'
            . '</patternFill>'
            . '</fill>';
        self::assertStringContainsString($expected, $data, 'conditional 1');
        $expected = '<dxf><fill>'
            . '<patternFill patternType="solid">'
            . '<bgColor rgb="FFFFFF00"/>'
            . '</patternFill>'
            . '</fill>';
        self::assertStringContainsString($expected, $data, 'conditional 2');
    }
}
