<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Html;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Html as HtmlWriter;
use PHPUnit\Framework\TestCase;

class CalcErrorTest extends TestCase
{
    public function testCalcError(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', '={');
        $writer = new HtmlWriter($spreadsheet);
        $html = $writer->generateHtmlAll();
        self::assertStringContainsString('<td data-type="e" class="column0 style0 e">#ERROR</td>', $html);
        $spreadsheet->disconnectWorksheets();
    }
}
