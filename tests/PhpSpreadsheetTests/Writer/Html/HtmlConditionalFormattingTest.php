<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Html;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpOffice\PhpSpreadsheet\Writer\Html as HtmlWriter;
use PHPUnit\Framework\TestCase;

class HtmlConditionalFormattingTest extends TestCase
{
    private string $data = '';

    protected function setUp(): void
    {
        $file = 'tests/data/Writer/Html/HtmlConditionalFormattingTest.xlsx';
        $reader = new XlsxReader();
        $spreadsheet = $reader->load($file);
        $writer = new HtmlWriter($spreadsheet);
        $writer->setConditionalFormatting(true);
        $this->data = $writer->generateHtmlAll();
        $spreadsheet->disconnectWorksheets();
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('conditionalFormattingProvider')]
    public function testConditionalFormattingHtmlOutput(int $rowNumber, array $expectedMatches): void
    {
        self::assertSame(1, preg_match('~<tr class="row' . $rowNumber . '".+?</tr>~ms', $this->data, $matches));
        foreach ($expectedMatches as $i => $expected) {
            self::assertStringContainsString($expected, $matches[0]);
        }
    }

    public static function conditionalFormattingProvider(): array
    {
        return [
            'row 0' => [0, ['<td class="column1 style1 s">Jan</td>',
                '<td class="column2 style1 s">Feb</td>',
                '<td class="column3 style1 s">Mar</td>',
                '<td class="column4 style1 s">Apr</td>',
                '<td class="column5 style1 s">May</td>',
                '<td class="column6 style1 s">Jun</td>',
                '<td class="column7 style1 s">Jul</td>',
                '<td class="column8 style1 s">Aug</td>',
                '<td class="column9 style1 s">Sep</td>',
                '<td class="column10 style1 s">Oct</td>',
                '<td class="column11 style1 s">Nov</td>',
                '<td class="column12 style1 s">Dec</td>']],
            'row 1' => [1, ['<td class="column0 style1 s">Line A</td>',
                '<td class="column1 style1 n" style="vertical-align:bottom;border-bottom:1px solid #000000 !important;border-top:1px solid #000000 !important;border-left:1px solid #000000 !important;border-right:1px solid #000000 !important;color:#006100;font-family:\'Arial\';font-size:11pt;background-color:#C6EFCE;">110</td>',
                '<td class="column2 style1 n" style="vertical-align:bottom;border-bottom:1px solid #000000 !important;border-top:1px solid #000000 !important;border-left:1px solid #000000 !important;border-right:1px solid #000000 !important;color:#006100;font-family:\'Arial\';font-size:11pt;background-color:#C6EFCE;">110</td>',
                '<td class="column3 style1 n" style="vertical-align:bottom;border-bottom:1px solid #000000 !important;border-top:1px solid #000000 !important;border-left:1px solid #000000 !important;border-right:1px solid #000000 !important;color:#006100;font-family:\'Arial\';font-size:11pt;background-color:#C6EFCE;">110</td>',
                '<td class="column4 style1 n" style="vertical-align:bottom;border-bottom:1px solid #000000 !important;border-top:1px solid #000000 !important;border-left:1px solid #000000 !important;border-right:1px solid #000000 !important;color:#006100;font-family:\'Arial\';font-size:11pt;background-color:#C6EFCE;">110</td>',
                '<td class="column5 style1 n" style="vertical-align:bottom;border-bottom:1px solid #000000 !important;border-top:1px solid #000000 !important;border-left:1px solid #000000 !important;border-right:1px solid #000000 !important;color:#006100;font-family:\'Arial\';font-size:11pt;background-color:#C6EFCE;">120</td>',
                '<td class="column6 style1 n" style="vertical-align:bottom;border-bottom:1px solid #000000 !important;border-top:1px solid #000000 !important;border-left:1px solid #000000 !important;border-right:1px solid #000000 !important;color:#006100;font-family:\'Arial\';font-size:11pt;background-color:#C6EFCE;">110</td>',
                '<td class="column7 style1 n" style="vertical-align:bottom;border-bottom:1px solid #000000 !important;border-top:1px solid #000000 !important;border-left:1px solid #000000 !important;border-right:1px solid #000000 !important;color:#9C5700;font-family:\'Arial\';font-size:11pt;background-color:#FFEB9C;">90</td>',
                '<td class="column8 style1 n" style="vertical-align:bottom;border-bottom:1px solid #000000 !important;border-top:1px solid #000000 !important;border-left:1px solid #000000 !important;border-right:1px solid #000000 !important;color:#006100;font-family:\'Arial\';font-size:11pt;background-color:#C6EFCE;">110</td>',
                '<td class="column9 style1 n" style="vertical-align:bottom;border-bottom:1px solid #000000 !important;border-top:1px solid #000000 !important;border-left:1px solid #000000 !important;border-right:1px solid #000000 !important;color:#006100;font-family:\'Arial\';font-size:11pt;background-color:#C6EFCE;">110</td>',
                '<td class="column10 style1 n" style="vertical-align:bottom;border-bottom:1px solid #000000 !important;border-top:1px solid #000000 !important;border-left:1px solid #000000 !important;border-right:1px solid #000000 !important;color:#006100;font-family:\'Arial\';font-size:11pt;background-color:#C6EFCE;">110</td>',
                '<td class="column11 style1 n" style="vertical-align:bottom;border-bottom:1px solid #000000 !important;border-top:1px solid #000000 !important;border-left:1px solid #000000 !important;border-right:1px solid #000000 !important;color:#006100;font-family:\'Arial\';font-size:11pt;background-color:#C6EFCE;">110</td>',
                '<td class="column12 style1 n" style="vertical-align:bottom;border-bottom:1px solid #000000 !important;border-top:1px solid #000000 !important;border-left:1px solid #000000 !important;border-right:1px solid #000000 !important;color:#006100;font-family:\'Arial\';font-size:11pt;background-color:#C6EFCE;">110</td>']],
            'row 2' => [2, ['<td class="column0 style1 s">Line B</td>',
                '<td class="column1 style1 n" style="vertical-align:bottom;border-bottom:1px solid #000000 !important;border-top:1px solid #000000 !important;border-left:1px solid #000000 !important;border-right:1px solid #000000 !important;color:#006100;font-family:\'Arial\';font-size:11pt;background-color:#C6EFCE;">70</td>',
                '<td class="column2 style1 n" style="vertical-align:bottom;border-bottom:1px solid #000000 !important;border-top:1px solid #000000 !important;border-left:1px solid #000000 !important;border-right:1px solid #000000 !important;color:#006100;font-family:\'Arial\';font-size:11pt;background-color:#C6EFCE;">80</td>',
                '<td class="column3 style1 n" style="vertical-align:bottom;border-bottom:1px solid #000000 !important;border-top:1px solid #000000 !important;border-left:1px solid #000000 !important;border-right:1px solid #000000 !important;color:#006100;font-family:\'Arial\';font-size:11pt;background-color:#C6EFCE;">70</td>',
                '<td class="column4 style1 n" style="vertical-align:bottom;border-bottom:1px solid #000000 !important;border-top:1px solid #000000 !important;border-left:1px solid #000000 !important;border-right:1px solid #000000 !important;color:#006100;font-family:\'Arial\';font-size:11pt;background-color:#C6EFCE;">70</td>',
                '<td class="column5 style1 n" style="vertical-align:bottom;border-bottom:1px solid #000000 !important;border-top:1px solid #000000 !important;border-left:1px solid #000000 !important;border-right:1px solid #000000 !important;color:#006100;font-family:\'Arial\';font-size:11pt;background-color:#C6EFCE;">70</td>',
                '<td class="column6 style1 n" style="vertical-align:bottom;border-bottom:1px solid #000000 !important;border-top:1px solid #000000 !important;border-left:1px solid #000000 !important;border-right:1px solid #000000 !important;color:#006100;font-family:\'Arial\';font-size:11pt;background-color:#C6EFCE;">70</td>',
                '<td class="column7 style1 n" style="vertical-align:bottom;border-bottom:1px solid #000000 !important;border-top:1px solid #000000 !important;border-left:1px solid #000000 !important;border-right:1px solid #000000 !important;color:#9C5700;font-family:\'Arial\';font-size:11pt;background-color:#FFEB9C;">60</td>',
                '<td class="column8 style1 n" style="vertical-align:bottom;border-bottom:1px solid #000000 !important;border-top:1px solid #000000 !important;border-left:1px solid #000000 !important;border-right:1px solid #000000 !important;color:#006100;font-family:\'Arial\';font-size:11pt;background-color:#C6EFCE;">70</td>',
                '<td class="column9 style1 n" style="vertical-align:bottom;border-bottom:1px solid #000000 !important;border-top:1px solid #000000 !important;border-left:1px solid #000000 !important;border-right:1px solid #000000 !important;color:#006100;font-family:\'Arial\';font-size:11pt;background-color:#C6EFCE;">70</td>',
                '<td class="column10 style1 n" style="vertical-align:bottom;border-bottom:1px solid #000000 !important;border-top:1px solid #000000 !important;border-left:1px solid #000000 !important;border-right:1px solid #000000 !important;color:#006100;font-family:\'Arial\';font-size:11pt;background-color:#C6EFCE;">70</td>',
                '<td class="column11 style1 n" style="vertical-align:bottom;border-bottom:1px solid #000000 !important;border-top:1px solid #000000 !important;border-left:1px solid #000000 !important;border-right:1px solid #000000 !important;color:#006100;font-family:\'Arial\';font-size:11pt;background-color:#C6EFCE;">70</td>',
                '<td class="column12 style1 n" style="vertical-align:bottom;border-bottom:1px solid #000000 !important;border-top:1px solid #000000 !important;border-left:1px solid #000000 !important;border-right:1px solid #000000 !important;color:#006100;font-family:\'Arial\';font-size:11pt;background-color:#C6EFCE;">70</td>']],
            'row 3' => [3, ['<td class="column0 style1 s">Line C</td>',
                '<td class="column1 style1 n" style="vertical-align:bottom;border-bottom:1px solid #000000 !important;border-top:1px solid #000000 !important;border-left:1px solid #000000 !important;border-right:1px solid #000000 !important;color:#006100;font-family:\'Arial\';font-size:11pt;background-color:#C6EFCE;">1</td>',
                '<td class="column2 style1 n" style="vertical-align:bottom;border-bottom:1px solid #000000 !important;border-top:1px solid #000000 !important;border-left:1px solid #000000 !important;border-right:1px solid #000000 !important;color:#006100;font-family:\'Arial\';font-size:11pt;background-color:#C6EFCE;">1</td>',
                '<td class="column3 style1 n" style="vertical-align:bottom;border-bottom:1px solid #000000 !important;border-top:1px solid #000000 !important;border-left:1px solid #000000 !important;border-right:1px solid #000000 !important;color:#006100;font-family:\'Arial\';font-size:11pt;background-color:#C6EFCE;">1</td>',
                '<td class="column4 style1 n" style="vertical-align:bottom;border-bottom:1px solid #000000 !important;border-top:1px solid #000000 !important;border-left:1px solid #000000 !important;border-right:1px solid #000000 !important;color:#006100;font-family:\'Arial\';font-size:11pt;background-color:#C6EFCE;">1</td>',
                '<td class="column5 style1 n" style="vertical-align:bottom;border-bottom:1px solid #000000 !important;border-top:1px solid #000000 !important;border-left:1px solid #000000 !important;border-right:1px solid #000000 !important;color:#006100;font-family:\'Arial\';font-size:11pt;background-color:#C6EFCE;">1</td>',
                '<td class="column6 style1 n" style="vertical-align:bottom;border-bottom:1px solid #000000 !important;border-top:1px solid #000000 !important;border-left:1px solid #000000 !important;border-right:1px solid #000000 !important;color:#006100;font-family:\'Arial\';font-size:11pt;background-color:#C6EFCE;">1</td>',
                '<td class="column7 style1 n" style="vertical-align:bottom;border-bottom:1px solid #000000 !important;border-top:1px solid #000000 !important;border-left:1px solid #000000 !important;border-right:1px solid #000000 !important;color:#006100;font-family:\'Arial\';font-size:11pt;background-color:#C6EFCE;">1</td>',
                '<td class="column8 style1 n" style="vertical-align:bottom;border-bottom:1px solid #000000 !important;border-top:1px solid #000000 !important;border-left:1px solid #000000 !important;border-right:1px solid #000000 !important;color:#006100;font-family:\'Arial\';font-size:11pt;background-color:#C6EFCE;">1</td>',
                '<td class="column9 style1 n" style="vertical-align:bottom;border-bottom:1px solid #000000 !important;border-top:1px solid #000000 !important;border-left:1px solid #000000 !important;border-right:1px solid #000000 !important;color:#006100;font-family:\'Arial\';font-size:11pt;background-color:#C6EFCE;">1</td>',
                '<td class="column10 style1 n" style="vertical-align:bottom;border-bottom:1px solid #000000 !important;border-top:1px solid #000000 !important;border-left:1px solid #000000 !important;border-right:1px solid #000000 !important;color:#006100;font-family:\'Arial\';font-size:11pt;background-color:#C6EFCE;">1</td>',
                '<td class="column11 style1 n" style="vertical-align:bottom;border-bottom:1px solid #000000 !important;border-top:1px solid #000000 !important;border-left:1px solid #000000 !important;border-right:1px solid #000000 !important;color:#9C0006;font-family:\'Arial\';font-size:11pt;background-color:#FFC7CE;">5</td>',
                '<td class="column12 style1 n" style="vertical-align:bottom;border-bottom:1px solid #000000 !important;border-top:1px solid #000000 !important;border-left:1px solid #000000 !important;border-right:1px solid #000000 !important;color:#006100;font-family:\'Arial\';font-size:11pt;background-color:#C6EFCE;">1</td>']],
            'row 4' => [4, ['<td class="column0 style1 s">Line D</td>',
                '<td class="column1 style1 n">1</td>',
                '<td class="column2 style1 n">1</td>',
                '<td class="column3 style1 n">1</td>',
                '<td class="column4 style1 n">1</td>',
                '<td class="column5 style1 n">0</td>',
                '<td class="column6 style1 n">1</td>',
                '<td class="column7 style1 n">1</td>',
                '<td class="column8 style1 n">0</td>',
                '<td class="column9 style1 n">1</td>',
                '<td class="column10 style1 n">1</td>',
                '<td class="column11 style1 n">5</td>',
                '<td class="column12 style1 n">1</td>']]];
    }
}
