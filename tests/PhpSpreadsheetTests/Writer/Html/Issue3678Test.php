<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Html;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Html;
use PHPUnit\Framework\TestCase;

class Issue3678Test extends TestCase
{
    public function testInlineAndNot(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getCell('A1')->setValue(1);
        $styleArray = [
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'color' => ['rgb' => 'FFFF00'],
            ],
        ];
        $sheet->getStyle('A1')->applyFromArray($styleArray);
        $style1 = "vertical-align:bottom; border-bottom:none #000000; border-top:none #000000; border-left:none #000000; border-right:none #000000; color:#000000; font-family:'Calibri'; font-size:11pt; background-color:#FFFF00";
        $style2 = "vertical-align:bottom; color:#000000; font-family:'Calibri'; font-size:11pt; background-color:#FFFF00";
        $style2 .= '; text-align:right; width:42pt';
        $writer = new Html($spreadsheet);
        $html = $writer->generateHtmlAll();
        self::assertStringContainsString('td.style1, th.style1 { ' . $style1 . ' }', $html);
        self::assertStringContainsString('<td class="column0 style1 n">1</td>', $html);
        self::assertStringContainsString('table.sheet0 col.col0 { width:42pt }', $html);
        self::assertStringContainsString('.n { text-align:right }', $html);
        $writer->setUseInlineCss(true);
        $html = $writer->generateHtmlAll();
        self::assertStringContainsString('<td style="' . $style2 . '" class="gridlines gridlinesp">1</td>', $html);
        $spreadsheet->disconnectWorksheets();
    }
}
