<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Conditional;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;
use PHPUnit\Framework\TestCase;

class Issue4025Test extends TestCase
{
    private static function merge(Worksheet $sheet, string $start, string $end): void
    {
        $sheet->mergeCells("$start:$end");
        $sheet->getStyle($start)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle($start)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
    }

    public function testIssue4025(): void
    {
        // Writing alignment in conditional style caused weirdness
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setRightToLeft(true);
        $sheet->setCellValue('E1', 'אושר עד');
        $sheet->setCellValue('M1', 'ויקטורי');
        $sheet->setCellValue('E2', '2024-05-01');
        $sheet->setCellValue('H2', '2024-05-12');
        $sheet->setCellValue('M2', '2024-05-01');
        $sheet->setCellValue('P2', '2024-05-12');
        $sheet->setCellValue('A3', 'ברקוד');
        $sheet->setCellValue('B3', 'תיאור מוצר');
        $sheet->setCellValue('C3', 'קטגוריה');
        $sheet->setCellValue('D3', 'יצרן');
        $sheet->setCellValue('E3', 'מחיר');
        $sheet->setCellValue('F3', 'מבצע');
        $sheet->setCellValue('G3', 'קובע');
        $sheet->setCellValue('H3', 'מחיר');
        $sheet->setCellValue('I3', 'מבצע');
        $sheet->setCellValue('J3', 'קובע');
        $sheet->setCellValue('K3', 'הפרש ב-₪');
        $sheet->setCellValue('L3', 'הפרש ב-₪');
        $sheet->setCellValue('M3', 'מחיר');
        $sheet->setCellValue('N3', 'מבצע');
        $sheet->setCellValue('O3', 'קובע');
        $sheet->setCellValue('P3', 'מחיר');
        $sheet->setCellValue('Q3', 'מבצע');
        $sheet->setCellValue('R3', 'קובע');
        $sheet->setCellValue('S3', 'הפרש ב-₪');
        $sheet->setCellValue('T3', 'הפרש ב-₪');
        $sheet->setCellValue('A4', '7290010945306');
        $sheet->setCellValue('B4', 'חלב טרי בקרטון 3% בד"צ טרה 2 ליטר');
        $sheet->setCellValue('C4', 'חלב טרי');
        $sheet->setCellValue('D4', 'החברה המרכזית ל.מ.ק - טרה');

        $sheet->setCellValue('E4', 13.50);
        $sheet->setCellValue('F4', '-');
        //$sheet->setCellValue('G4', 13.50);
        $sheet->setCellValue('G4', 14.70);
        $sheet->setCellValue('H4', 14.20);
        $sheet->setCellValue('I4', '-');
        $sheet->setCellValue('J4', 14.20);
        $sheet->setCellValue('K4', 0.70);
        $sheet->setCellValue('L4', 0.0519);
        $sheet->setCellValue('M4', 13.62);
        $sheet->setCellValue('N4', '-');
        $sheet->setCellValue('O4', 13.62);
        $sheet->setCellValue('P4', 14.22);
        $sheet->setCellValue('Q4', '-');
        $sheet->setCellValue('R4', 14.22);
        $sheet->setCellValue('S4', 0.60);
        $sheet->setCellValue('T4', 0.0441);

        $sheet->setCellValue('E10', '=SUM(G4:G10)');
        $sheet->setCellValue('H10', '=SUM(J4:J10)');
        $sheet->setCellValue('K10', '=COUNTIF(K4:K9,">0")');
        $sheet->setCellValue('M10', '=SUM(O4:O10)');
        $sheet->setCellValue('P10', '=SUM(R4:R10)');
        $sheet->setCellValue('S10', '=COUNTIF(S4:S9,">0")');
        $sheet->setCellValue('H11', '=(SUM(J4:J10) / SUM(G4:G10) - 1)');
        $sheet->setCellValue('K11', '=COUNTIF(K4:K9,"<0")');
        $sheet->setCellValue('P11', '=(SUM(R4:R10) / SUM(O4:O10) - 1)');
        $sheet->setCellValue('S11', '=COUNTIF(S4:S9,"<0")');

        $sheet->setAutoFilter('A3:T3');
        self::merge($sheet, 'E1', 'L1');
        self::merge($sheet, 'E2', 'G2');
        self::merge($sheet, 'H2', 'J2');
        self::merge($sheet, 'K2', 'L2');
        self::merge($sheet, 'M1', 'T1');
        self::merge($sheet, 'M2', 'O2');
        self::merge($sheet, 'P2', 'R2');
        self::merge($sheet, 'S2', 'T2');
        self::merge($sheet, 'E10', 'G10');
        self::merge($sheet, 'H10', 'J10');
        self::merge($sheet, 'H11', 'J11');
        self::merge($sheet, 'M10', 'O10');
        self::merge($sheet, 'P10', 'R10');
        self::merge($sheet, 'P11', 'R11');
        self::merge($sheet, 'K10', 'L10');
        self::merge($sheet, 'K11', 'L11');
        self::merge($sheet, 'S10', 'T10');
        self::merge($sheet, 'S11', 'T11');

        $sheet->getStyle('E4:K8')->getNumberFormat()->setFormatCode('0.00');
        $sheet->getStyle('L4:L8')->getNumberFormat()->setFormatCode('0.00%');
        $sheet->getStyle('M4:S8')->getNumberFormat()->setFormatCode('0.00');
        $sheet->getStyle('T4:T8')->getNumberFormat()->setFormatCode('0.00%');
        $sheet->getStyle('P10')->getNumberFormat()->setFormatCode('0.00');
        $sheet->getStyle('P11')->getNumberFormat()->setFormatCode('0.00%');
        $sheet->getStyle('H1')->getNumberFormat()->setFormatCode('0.00');
        $sheet->getStyle('H11')->getNumberFormat()->setFormatCode('0.00%');

        $center = [
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ];
        $pink = [
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'font' => ['color' => ['rgb' => 'FF0000']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'endColor' => ['argb' => 'FFFFE5E8'], 'startColor' => ['argb' => 'FFFFE5E8']],
        ];
        $green = [
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'font' => ['color' => ['rgb' => '339966']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'endColor' => ['argb' => 'ffccffcc'], 'startColor' => ['argb' => 'ffccffcc']],
        ];
        //$sheet->getStyle('E4:T8')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('E4:T8')->applyFromArray($center);
        $sheet->getStyle('H4')->applyFromArray($pink);
        $sheet->getStyle('J4:L4')->applyFromArray($pink);
        $sheet->getStyle('P4')->applyFromArray($pink);
        $sheet->getStyle('R4:T4')->applyFromArray($pink);
        $sheet->getStyle('K10')->applyFromArray($pink);
        $sheet->getStyle('S10')->applyFromArray($pink);
        $sheet->getStyle('K11')->applyFromArray($green);
        $sheet->getStyle('S11')->applyFromArray($green);

        $condition1a = new Conditional();
        $condition1a->setConditionType(Conditional::CONDITION_EXPRESSION);
        $condition1a->addCondition('$H$10 < $E$10');
        $condition1a->getStyle()->applyFromArray($green);
        $condition1b = new Conditional();
        $condition1b->setConditionType(Conditional::CONDITION_EXPRESSION);
        $condition1b->addCondition('$H$10 > $E$10');
        $condition1b->getStyle()->applyFromArray($pink);
        $conditionalStyles = [$condition1a, $condition1b];
        $sheet->getStyle('H10:H11')->setConditionalStyles($conditionalStyles);

        $condition2a = new Conditional();
        $condition2a->setConditionType(Conditional::CONDITION_EXPRESSION);
        $condition2a->addCondition('$P$10 < $M$10');
        $condition2a->getStyle()->applyFromArray($green);
        $condition2b = new Conditional();
        $condition2b->setConditionType(Conditional::CONDITION_EXPRESSION);
        $condition2b->addCondition('$P$10 > $M$10');
        $condition2b->getStyle()->applyFromArray($pink);
        $conditionalStyles2 = [$condition2a, $condition2b];
        $sheet->getStyle('P10:P11')->setConditionalStyles($conditionalStyles2);

        $sheet->setSelectedCells('A1');
        $writer = new XlsxWriter($spreadsheet);

        $writer->getStylesConditionalHashTable()->addFromSource($writer->getWriterPartStyle()->allConditionalStyles($spreadsheet));
        $writerStyle = new XlsxWriter\Style($writer);
        $data = $writerStyle->writeStyles($spreadsheet);
        $spreadsheet->disconnectWorksheets();

        $preg = preg_match('~<dxfs.*</dxfs>~ms', $data, $matches);
        if ($preg !== 1) {
            self::fail('preg failed');
        } else {
            self::assertStringContainsString('<font', $matches[0]);
            self::assertStringContainsString('<fill', $matches[0]);
            self::assertStringNotContainsString('<align', $matches[0]);
        }
    }
}
