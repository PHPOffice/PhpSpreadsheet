<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Html;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpOffice\PhpSpreadsheet\Writer\Html as HtmlWriter;
use PHPUnit\Framework\TestCase;

class HtmlColourScaleTest extends TestCase
{
    private string $data = '';

    protected function setUp(): void
    {
        $file = 'samples/templates/ColourScale.xlsx';
        $reader = new XlsxReader();
        $spreadsheet = $reader->load($file);
        $writer = new HtmlWriter($spreadsheet);
        $writer->setConditionalFormatting(true);
        $this->data = $writer->generateHtmlAll();
        $spreadsheet->disconnectWorksheets();
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('colourScaleProvider')]
    public function testColourScaleHtmlOutput(int $rowNumber, array $expectedMatches): void
    {
        self::assertSame(1, preg_match('~<tr class="row' . $rowNumber . '".+?</tr>~ms', $this->data, $matches));
        foreach ($expectedMatches as $i => $expected) {
            self::assertStringContainsString($expected, $matches[0]);
        }
    }

    public static function colourScaleProvider(): array
    {
        return [
            'row 0: low/high min/max with 80% midpoint' => [0, ['<td class="column0 style1 n" style="vertical-align:bottom;border-bottom:none #000000;border-top:none #000000;border-left:none #000000;border-right:none #000000;color:#000000;font-family:\'Arial\';font-size:11pt;background-color:#57BB8A;">1</td>',
                '<td class="column1 style1 n" style="vertical-align:bottom;border-bottom:none #000000;border-top:none #000000;border-left:none #000000;border-right:none #000000;color:#000000;font-family:\'Arial\';font-size:11pt;background-color:#6EBE85;">2</td>',
                '<td class="column2 style1 n" style="vertical-align:bottom;border-bottom:none #000000;border-top:none #000000;border-left:none #000000;border-right:none #000000;color:#000000;font-family:\'Arial\';font-size:11pt;background-color:#85C280;">3</td>',
                '<td class="column3 style1 n" style="vertical-align:bottom;border-bottom:none #000000;border-top:none #000000;border-left:none #000000;border-right:none #000000;color:#000000;font-family:\'Arial\';font-size:11pt;background-color:#9DC67A;">4</td>',
                '<td class="column4 style1 n" style="vertical-align:bottom;border-bottom:none #000000;border-top:none #000000;border-left:none #000000;border-right:none #000000;color:#000000;font-family:\'Arial\';font-size:11pt;background-color:#B4CA76;">5</td>',
                '<td class="column5 style1 n" style="vertical-align:bottom;border-bottom:none #000000;border-top:none #000000;border-left:none #000000;border-right:none #000000;color:#000000;font-family:\'Arial\';font-size:11pt;background-color:#CBCD71;">6</td>',
                '<td class="column6 style1 n" style="vertical-align:bottom;border-bottom:none #000000;border-top:none #000000;border-left:none #000000;border-right:none #000000;color:#000000;font-family:\'Arial\';font-size:11pt;background-color:#E3D16C;">7</td>',
                '<td class="column7 style1 n" style="vertical-align:bottom;border-bottom:none #000000;border-top:none #000000;border-left:none #000000;border-right:none #000000;color:#000000;font-family:\'Arial\';font-size:11pt;background-color:#FAD566;">8</td>',
                '<td class="column8 style1 n" style="vertical-align:bottom;border-bottom:none #000000;border-top:none #000000;border-left:none #000000;border-right:none #000000;color:#000000;font-family:\'Arial\';font-size:11pt;background-color:#F3AD6B;">9</td>',
                '<td class="column9 style1 n" style="vertical-align:bottom;border-bottom:none #000000;border-top:none #000000;border-left:none #000000;border-right:none #000000;color:#000000;font-family:\'Arial\';font-size:11pt;background-color:#E67C73;">10</td>']],
            'row 1: low/high 40%/80% with 50% midpoint' => [1, ['<td class="column0 style1 n" style="vertical-align:bottom;border-bottom:none #000000;border-top:none #000000;border-left:none #000000;border-right:none #000000;color:#000000;font-family:\'Arial\';font-size:11pt;background-color:#57BB8A;">1</td>',
                '<td class="column1 style1 n" style="vertical-align:bottom;border-bottom:none #000000;border-top:none #000000;border-left:none #000000;border-right:none #000000;color:#000000;font-family:\'Arial\';font-size:11pt;background-color:#57BB8A;">2</td>',
                '<td class="column2 style1 n" style="vertical-align:bottom;border-bottom:none #000000;border-top:none #000000;border-left:none #000000;border-right:none #000000;color:#000000;font-family:\'Arial\';font-size:11pt;background-color:#57BB8A;">3</td>',
                '<td class="column3 style1 n" style="vertical-align:bottom;border-bottom:none #000000;border-top:none #000000;border-left:none #000000;border-right:none #000000;color:#000000;font-family:\'Arial\';font-size:11pt;background-color:#57BB8A;">4</td>',
                '<td class="column4 style1 n" style="vertical-align:bottom;border-bottom:none #000000;border-top:none #000000;border-left:none #000000;border-right:none #000000;color:#000000;font-family:\'Arial\';font-size:11pt;background-color:#A1C77A;">5</td>',
                '<td class="column5 style1 n" style="vertical-align:bottom;border-bottom:none #000000;border-top:none #000000;border-left:none #000000;border-right:none #000000;color:#000000;font-family:\'Arial\';font-size:11pt;background-color:#F1A36D;">6</td>',
                '<td class="column6 style1 n" style="vertical-align:bottom;border-bottom:none #000000;border-top:none #000000;border-left:none #000000;border-right:none #000000;color:#000000;font-family:\'Arial\';font-size:11pt;background-color:#E67C73;">7</td>',
                '<td class="column7 style1 n" style="vertical-align:bottom;border-bottom:none #000000;border-top:none #000000;border-left:none #000000;border-right:none #000000;color:#000000;font-family:\'Arial\';font-size:11pt;background-color:#E67C73;">8</td>',
                '<td class="column8 style1 n" style="vertical-align:bottom;border-bottom:none #000000;border-top:none #000000;border-left:none #000000;border-right:none #000000;color:#000000;font-family:\'Arial\';font-size:11pt;background-color:#E67C73;">9</td>',
                '<td class="column9 style1 n" style="vertical-align:bottom;border-bottom:none #000000;border-top:none #000000;border-left:none #000000;border-right:none #000000;color:#000000;font-family:\'Arial\';font-size:11pt;background-color:#E67C73;">10</td>']],
            'row 2: low/high/midpoint values 3/8/4 ' => [2, ['<td class="column0 style1 n" style="vertical-align:bottom;border-bottom:none #000000;border-top:none #000000;border-left:none #000000;border-right:none #000000;color:#000000;font-family:\'Arial\';font-size:11pt;background-color:#57BB8A;">1</td>',
                '<td class="column1 style1 n" style="vertical-align:bottom;border-bottom:none #000000;border-top:none #000000;border-left:none #000000;border-right:none #000000;color:#000000;font-family:\'Arial\';font-size:11pt;background-color:#57BB8A;">2</td>',
                '<td class="column2 style1 n" style="vertical-align:bottom;border-bottom:none #000000;border-top:none #000000;border-left:none #000000;border-right:none #000000;color:#000000;font-family:\'Arial\';font-size:11pt;background-color:#57BB8A;">3</td>',
                '<td class="column3 style1 n" style="vertical-align:bottom;border-bottom:none #000000;border-top:none #000000;border-left:none #000000;border-right:none #000000;color:#000000;font-family:\'Arial\';font-size:11pt;background-color:#FFD666;">4</td>',
                '<td class="column4 style1 n" style="vertical-align:bottom;border-bottom:none #000000;border-top:none #000000;border-left:none #000000;border-right:none #000000;color:#000000;font-family:\'Arial\';font-size:11pt;background-color:#F8BF69;">5</td>',
                '<td class="column5 style1 n" style="vertical-align:bottom;border-bottom:none #000000;border-top:none #000000;border-left:none #000000;border-right:none #000000;color:#000000;font-family:\'Arial\';font-size:11pt;background-color:#F2A96C;">6</td>',
                '<td class="column6 style1 n" style="vertical-align:bottom;border-bottom:none #000000;border-top:none #000000;border-left:none #000000;border-right:none #000000;color:#000000;font-family:\'Arial\';font-size:11pt;background-color:#EC926F;">7</td>',
                '<td class="column7 style1 n" style="vertical-align:bottom;border-bottom:none #000000;border-top:none #000000;border-left:none #000000;border-right:none #000000;color:#000000;font-family:\'Arial\';font-size:11pt;background-color:#E67C73;">8</td>',
                '<td class="column8 style1 n" style="vertical-align:bottom;border-bottom:none #000000;border-top:none #000000;border-left:none #000000;border-right:none #000000;color:#000000;font-family:\'Arial\';font-size:11pt;background-color:#E67C73;">9</td>',
                '<td class="column9 style1 n" style="vertical-align:bottom;border-bottom:none #000000;border-top:none #000000;border-left:none #000000;border-right:none #000000;color:#000000;font-family:\'Arial\';font-size:11pt;background-color:#E67C73;">10</td>']],
            'row 3: low/high with 30/80 percentile and 50% midpoint, one cell no value' => [3, ['<td class="column0 style1 n" style="vertical-align:bottom;border-bottom:none #000000;border-top:none #000000;border-left:none #000000;border-right:none #000000;color:#000000;font-family:\'Arial\';font-size:11pt;background-color:#57BB8A;">1</td>',
                '<td class="column1 style1 n" style="vertical-align:bottom;border-bottom:none #000000;border-top:none #000000;border-left:none #000000;border-right:none #000000;color:#000000;font-family:\'Arial\';font-size:11pt;background-color:#57BB8A;">2</td>',
                '<td class="column2 style1 n" style="vertical-align:bottom;border-bottom:none #000000;border-top:none #000000;border-left:none #000000;border-right:none #000000;color:#000000;font-family:\'Arial\';font-size:11pt;background-color:#8FC47E;">3</td>',
                '<td class="column3 style1 n" style="vertical-align:bottom;border-bottom:none #000000;border-top:none #000000;border-left:none #000000;border-right:none #000000;color:#000000;font-family:\'Arial\';font-size:11pt;background-color:#C7CD72;">4</td>',
                '<td class="column4 style1 n" style="vertical-align:bottom;border-bottom:none #000000;border-top:none #000000;border-left:none #000000;border-right:none #000000;color:#000000;font-family:\'Arial\';font-size:11pt;background-color:#57BB8A;">2</td>',
                '<td class="column5 style1 n" style="vertical-align:bottom;border-bottom:none #000000;border-top:none #000000;border-left:none #000000;border-right:none #000000;color:#000000;font-family:\'Arial\';font-size:11pt;background-color:#E67C73;">9</td>',
                '<td class="column6 style1 n" style="vertical-align:bottom;border-bottom:none #000000;border-top:none #000000;border-left:none #000000;border-right:none #000000;color:#000000;font-family:\'Arial\';font-size:11pt;background-color:#E67C73;">9</td>',
                '<td class="column7 style1 n" style="vertical-align:bottom;border-bottom:none #000000;border-top:none #000000;border-left:none #000000;border-right:none #000000;color:#000000;font-family:\'Arial\';font-size:11pt;background-color:#E67C73;">9</td>',
                '<td class="column8 style2 null"></td>',
                '<td class="column9 style1 n" style="vertical-align:bottom;border-bottom:none #000000;border-top:none #000000;border-left:none #000000;border-right:none #000000;color:#000000;font-family:\'Arial\';font-size:11pt;background-color:#E67C73;">10</td>']]];
    }
}
