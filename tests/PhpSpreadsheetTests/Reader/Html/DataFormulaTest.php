<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Html;

use PhpOffice\PhpSpreadsheet\Reader\Html as HtmlReader;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Html as HtmlWriter;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class DataFormulaTest extends AbstractFunctional
{
    public function testDataFormula(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', true);
        $sheet->setCellValue('A2', false);
        $sheet->setCellValue('A3', false); // no checkbox
        $sheet->getStyle('A1')->setCheckBox(true);
        $sheet->getStyle('A2')->setCheckBox(true);
        $sheet->setCellValue('B1', '=AND(TRUE,TRUE)');
        $sheet->setCellValue('B2', '=AND(TRUE,FALSE)');
        $sheet->setCellValue('B3', '=AND(FALSE,TRUE)'); // no checkbox
        $sheet->getStyle('B1')->setCheckBox(true);
        $sheet->getStyle('B2')->setCheckBox(true);
        $sheet->setCellValue('C1', '="A"&"B"&"C"');
        $sheet->setCellValue('C2', 5);
        $sheet->setCellValue('C3', '=3+2');

        $writer = new HtmlWriter($spreadsheet);
        $this->writeDataFormula($writer);
        $content = $writer->generateHtmlAll();
        $expected = '<td data-type="b" class="column0 style1 b">☑</td>';
        self::assertStringContainsString($expected, $content, 'bool non-formula');
        $expected = '<td data-type="b" data-formula="=AND(TRUE,TRUE)" class="column1 style1 b">☑</td>';
        self::assertStringContainsString($expected, $content, 'bool formula');
        $expected = '<td data-type="s" data-formula="=&quot;A&quot;&amp;&quot;B&quot;&amp;&quot;C&quot;" class="column2 style0 s">ABC</td>';
        self::assertStringContainsString($expected, $content, 'string formula requiring escaped characters');
        $expected = '<td class="column2 style0 n">5</td>';
        self::assertStringContainsString($expected, $content, 'numeric non-formula');
        $expected = '<td data-type="n" data-formula="=3+2" class="column2 style0 n">5</td>';
        self::assertStringContainsString($expected, $content, 'numeric formula');

        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, 'Html', null, $this->writeDataFormula(...));
        $spreadsheet->disconnectWorksheets();

        $rsheet = $reloadedSpreadsheet->getActiveSheet();
        self::assertTrue($rsheet->getCell('A1')->getValue());
        self::assertFalse($rsheet->getCell('A2')->getValue());
        self::assertFalse($rsheet->getCell('A3')->getValue());
        self::assertTrue($rsheet->getStyle('A1')->getCheckBox());
        self::assertTrue($rsheet->getStyle('A2')->getCheckBox());
        self::assertFalse($rsheet->getStyle('A3')->getCheckBox());

        self::assertSame('=AND(TRUE,TRUE)', $rsheet->getCell('B1')->getValue());
        self::assertTrue(
            $rsheet->getCell('B1')->getOldCalculatedValue()
        );
        self::assertTrue(
            $rsheet->getCell('B1')->getCalculatedValue()
        );
        self::assertTrue($rsheet->getStyle('B1')->getCheckBox());

        self::assertSame('=AND(TRUE,FALSE)', $rsheet->getCell('B2')->getValue());
        self::assertFalse(
            $rsheet->getCell('B2')->getOldCalculatedValue()
        );
        self::assertFalse(
            $rsheet->getCell('B2')->getCalculatedValue()
        );
        self::assertTrue($rsheet->getStyle('B2')->getCheckBox());

        self::assertSame('=AND(FALSE,TRUE)', $rsheet->getCell('B3')->getValue());
        self::assertFalse(
            $rsheet->getCell('B3')->getOldCalculatedValue()
        );
        self::assertFalse(
            $rsheet->getCell('B3')->getCalculatedValue()
        );
        self::assertFalse($rsheet->getStyle('B3')->getCheckBox());

        self::assertSame('="A"&"B"&"C"', $rsheet->getCell('C1')->getValue());
        self::assertSame(
            'ABC',
            $rsheet->getCell('C1')->getOldCalculatedValue()
        );
        self::assertSame(
            'ABC',
            $rsheet->getCell('C1')->getCalculatedValue()
        );

        self::assertSame(5, $rsheet->getCell('C2')->getValue());
        self::assertNull(
            $rsheet->getCell('C2')->getOldCalculatedValue()
        );
        self::assertSame(
            5,
            $rsheet->getCell('C2')->getCalculatedValue()
        );

        self::assertSame('=3+2', $rsheet->getCell('C3')->getValue());
        self::assertSame(
            5,
            $rsheet->getCell('C3')->getOldCalculatedValue()
        );
        self::assertSame(
            5,
            $rsheet->getCell('C3')->getCalculatedValue()
        );

        $reloadedSpreadsheet->disconnectWorksheets();
    }

    private function writeDataFormula(HtmlWriter $writer): void
    {
        $writer->setDataFormula(true);
    }

    public function testNoPreCalculate(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', true);
        $sheet->setCellValue('A2', false);
        $sheet->setCellValue('A3', false); // no checkbox
        $sheet->getStyle('A1')->setCheckBox(true);
        $sheet->getStyle('A2')->setCheckBox(true);
        $sheet->setCellValue('B1', '=AND(TRUE,TRUE)');
        $sheet->setCellValue('B2', '=AND(TRUE,FALSE)');
        $sheet->setCellValue('B3', '=AND(FALSE,TRUE)'); // no checkbox
        $sheet->getStyle('B1')->setCheckBox(true);
        $sheet->getStyle('B2')->setCheckBox(true);
        $sheet->setCellValue('C1', '="A"&"B"&"C"');
        $sheet->setCellValue('C2', 5);
        $sheet->setCellValue('C3', '=3+2');

        $writer = new HtmlWriter($spreadsheet);
        $this->writeDataFormula($writer);
        $writer->setPreCalculateFormulas(false);
        $content = $writer->generateHtmlAll();
        $expected = '<td data-type="b" class="column0 style1 b">☑</td>';
        self::assertStringContainsString($expected, $content, 'bool non-formula');
        $expected = '<td data-checkbox="1" class="column1 style1 f">=AND(TRUE,TRUE)</td>';
        self::assertStringContainsString($expected, $content, 'bool formula');
        $expected = '<td class="column2 style0 f">=&quot;A&quot;&amp;&quot;B&quot;&amp;&quot;C&quot;</td>';
        self::assertStringContainsString($expected, $content, 'string formula requiring escaped characters');

        $spreadsheet->disconnectWorksheets();
        $reader = new HtmlReader();
        $reloadedSpreadsheet = $reader->loadFromString($content);
        $rsheet = $reloadedSpreadsheet->getActiveSheet();
        self::assertTrue($rsheet->getStyle('B1')->getCheckBox());
        $reloadedSpreadsheet->disconnectWorksheets();
    }
}
