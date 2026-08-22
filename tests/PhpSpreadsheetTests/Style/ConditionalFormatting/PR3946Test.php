<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Style\ConditionalFormatting;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Conditional;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Style;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;
use PHPUnit\Framework\TestCase;

class PR3946Test extends TestCase
{
    public function testConditionalTextInitialized(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $cellRange = 'C3:E5';
        $style = new Style();
        $style->applyFromArray([
            'fill' => [
                'color' => ['argb' => 'FFFFC000'],
                'fillType' => Fill::FILL_SOLID,
            ],
        ]);

        $condition = new Conditional();
        $condition->setConditionType(Conditional::CONDITION_CONTAINSTEXT);
        $condition->setOperatorType(Conditional::OPERATOR_CONTAINSTEXT);
        $condition->setStyle($style);
        $sheet->setConditionalStyles($cellRange, [$condition]);

        $writer = new XlsxWriter($spreadsheet);

        $writerWorksheet = new XlsxWriter\Worksheet($writer);
        $data = $writerWorksheet->writeWorksheet($sheet, []);
        self::assertStringContainsString('<conditionalFormatting sqref="C3:E5">', $data);
    }
}
