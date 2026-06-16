<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Html;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Html;
use PHPUnit\Framework\TestCase;

class TextColorTest extends TestCase
{
    public function testTextRotation(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 1);
        $sheet->setCellValue('B1', -1);
        $sheet->setCellValue('C1', 0);
        $sheet->setCellValue('D1', 'text');
        $format = '[Blue]General;[Red](0);[Green]General;[Magenta]General';
        $sheet->getStyle('A1:D1')->getNumberFormat()
            ->setFormatCode($format);

        $sheet->setCellValue('A2', 1);
        $sheet->setCellValue('B2', -1);
        $sheet->setCellValue('C2', 0);
        $sheet->setCellValue('D2', 'text');
        $format = '[Blue]General';
        $sheet->getStyle('A2:D2')->getNumberFormat()
            ->setFormatCode($format);

        $sheet->setCellValue('A3', 1);
        $sheet->setCellValue('B3', -1);
        $sheet->setCellValue('C3', 0);
        $sheet->setCellValue('D3', 'text');
        $format = '[Blue]General;[Red](0)';
        $sheet->getStyle('A3:D3')->getNumberFormat()
            ->setFormatCode($format);

        $sheet->setCellValue('A4', 1);
        $sheet->setCellValue('B4', -1);
        $sheet->setCellValue('C4', 0);
        $sheet->setCellValue('D4', 'text');
        $format = 'General;[Red](0)';
        $sheet->getStyle('A4:D4')->getNumberFormat()
            ->setFormatCode($format);

        $sheet->setCellValue('A5', 1);
        $sheet->setCellValue('B5', -1);
        $sheet->setCellValue('C5', 0);
        $sheet->setCellValue('D5', 'text');

        $writer = new Html($spreadsheet);
        $html = $writer->generateHtmlAll();
        $spreadsheet->disconnectWorksheets();
        $html = preg_replace('/^\s+/m', '', $html) ?? $html;
        $html = preg_replace('/\r?\n/m', '', $html) ?? $html;
        self::assertStringContainsString(
            '<tr class="row0">'
                . '<td class="column0 style1 n"><span style="color:blue">1</span></td>'
                . '<td class="column1 style1 n"><span style="color:red">(1)</span></td>'
                . '<td class="column2 style1 n"><span style="color:green">0</span></td>'
                . '<td class="column3 style1 s"><span style="color:magenta">text</span></td>'
                . '</tr>',
            $html
        );

        self::assertStringContainsString(
            '<tr class="row1">'
                . '<td class="column0 style2 n"><span style="color:blue">1</span></td>'
                . '<td class="column1 style2 n"><span style="color:blue">-1</span></td>'
                . '<td class="column2 style2 n"><span style="color:blue">0</span></td>'
                . '<td class="column3 style2 s"><span style="color:blue">text</span></td>'
                . '</tr>',
            $html
        );

        self::assertStringContainsString(
            '<tr class="row2">'
                . '<td class="column0 style3 n"><span style="color:blue">1</span></td>'
                . '<td class="column1 style3 n"><span style="color:red">(1)</span></td>'
                . '<td class="column2 style3 n"><span style="color:blue">0</span></td>'
                . '<td class="column3 style3 s"><span style="color:blue">text</span></td>'
                . '</tr>',
            $html
        );

        self::assertStringContainsString(
            '<tr class="row3">'
                . '<td class="column0 style4 n">1</td>'
                . '<td class="column1 style4 n"><span style="color:red">(1)</span></td>'
                . '<td class="column2 style4 n">0</td>'
                . '<td class="column3 style4 s">text</td>'
                . '</tr>',
            $html
        );

        self::assertStringContainsString(
            '<tr class="row4">'
                . '<td class="column0 style0 n">1</td>'
                . '<td class="column1 style0 n">-1</td>'
                . '<td class="column2 style0 n">0</td>'
                . '<td class="column3 style0 s">text</td>'
                . '</tr>',
            $html
        );
    }

    public function testEscape(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $payload = '<img src=x onerror=alert(document.domain)>';
        $formatCode = '[Green]General';
        $sheet->setCellValue('A1', $payload);
        $sheet->getStyle('A1')
            ->getNumberFormat()
            ->setFormatCode($formatCode);
        $writer = new Html($spreadsheet);
        $html = $writer->generateHtmlAll();
        $spreadsheet->disconnectWorksheets();
        self::assertStringContainsString(
            '<td class="column0 style1 s"><span style="color:green">&lt;img src=x onerror=alert(document.domain)&gt;</span></td>',
            $html
        );
    }
}
