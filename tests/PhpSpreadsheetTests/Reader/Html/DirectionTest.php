<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Html;

use PhpOffice\PhpSpreadsheet\Reader\Html as HtmlReader;
use PHPUnit\Framework\TestCase;

class DirectionTest extends TestCase
{
    public function testRtl(): void
    {
        $inlines = [
            "<table border='0' cellpadding='0' cellspacing='0' dir='rtl' id='sheet0' class='sheet0 gridlines'>",
            '<tbody>',
            '<tr class="row0">',
            '<td class="column0 style0 s">a1</td>',
            '<td class="column1 style0 s">b1</td>',
            '<td class="column2 style0 s">c1</td>',
            '</tr>',
            '<tr class="row1">',
            '<td class="column0 style0 s">a2</td>',
            '<td class="column1 style0 s">b2</td>',
            '<td class="column2 style0 s">c2</td>',
            '</tr>',
            '</tbody></table>',
        ];
        $html = implode("\n", $inlines);
        $reader = new HtmlReader();
        $spreadsheet = $reader->loadFromString($html);
        $sheet = $spreadsheet->getActiveSheet();
        self::assertTrue($sheet->getRightToLeft());
        self::assertSame('a1', $sheet->getCell('A1')->getValue());
        self::assertSame('c2', $sheet->getCell('C2')->getValue());
        $spreadsheet->disconnectWorksheets();
    }

    public function testLtr(): void
    {
        $inlines = [
            "<table border='0' cellpadding='0' cellspacing='0' dir='ltr' id='sheet0' class='sheet0 gridlines'>",
            '<tbody>',
            '<tr class="row0">',
            '<td class="column0 style0 s">a1</td>',
            '<td class="column1 style0 s">b1</td>',
            '<td class="column2 style0 s">c1</td>',
            '</tr>',
            '<tr class="row1">',
            '<td class="column0 style0 s">a2</td>',
            '<td class="column1 style0 s">b2</td>',
            '<td class="column2 style0 s">c2</td>',
            '</tr>',
            '</tbody></table>',
        ];
        $html = implode("\n", $inlines);
        $reader = new HtmlReader();
        $spreadsheet = $reader->loadFromString($html);
        $sheet = $spreadsheet->getActiveSheet();
        self::assertFalse($sheet->getRightToLeft());
        self::assertSame('a1', $sheet->getCell('A1')->getValue());
        self::assertSame('c2', $sheet->getCell('C2')->getValue());
        $spreadsheet->disconnectWorksheets();
    }

    public function testDefault(): void
    {
        $inlines = [
            "<table border='0' cellpadding='0' cellspacing='0' id='sheet0' class='sheet0 gridlines'>",
            '<tbody>',
            '<tr class="row0">',
            '<td class="column0 style0 s">a1</td>',
            '<td class="column1 style0 s">b1</td>',
            '<td class="column2 style0 s">c1</td>',
            '</tr>',
            '<tr class="row1">',
            '<td class="column0 style0 s">a2</td>',
            '<td class="column1 style0 s">b2</td>',
            '<td class="column2 style0 s">c2</td>',
            '</tr>',
            '</tbody></table>',
        ];
        $html = implode("\n", $inlines);
        $reader = new HtmlReader();
        $spreadsheet = $reader->loadFromString($html);
        $sheet = $spreadsheet->getActiveSheet();
        self::assertFalse($sheet->getRightToLeft());
        self::assertSame('a1', $sheet->getCell('A1')->getValue());
        self::assertSame('c2', $sheet->getCell('C2')->getValue());
        $spreadsheet->disconnectWorksheets();
    }
}
