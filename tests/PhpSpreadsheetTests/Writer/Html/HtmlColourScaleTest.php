<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Html;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
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

    private function extractCell(string $coordinate): string
    {
        [$column, $row] = Coordinate::indexesFromString($coordinate);
        --$column;
        --$row;
        // extract row into $matches
        $match = preg_match('~<tr class="row' . $row . '".+?</tr>~s', $this->data, $matches);
        if ($match !== 1) {
            return 'unable to match row';
        }
        $rowData = $matches[0];
        // extract cell into $matches
        $match = preg_match('~<td class="column' . $column . ' .+?</td>~s', $rowData, $matches);
        if ($match !== 1) {
            return 'unable to match column';
        }

        return $matches[0];
    }

    public function testColourScaleHtmlOutput(): void
    {
        $expectedMatches = [
            ['E1', 'background-color:#B4CA76;">5<', 'cell E1'],
            ['F1', 'background-color:#CBCD71;">6<', 'cell F1'],
            ['G1', 'background-color:#E3D16C;">7<', 'cell G1'],
            ['D2', 'background-color:#57BB8A;">4<', 'cell D2'],
            ['E2', 'background-color:#A1C77A;">5<', 'cell E2'],
            ['F2', 'background-color:#F1A36D;">6<', 'cell F2'],
            ['D3', 'background-color:#FFD666;">4<', 'cell D3'],
            ['G3', 'background-color:#EC926F;">7<', 'cell G3'],
            ['H3', 'background-color:#E67C73;">8<', 'cell H3'],
            ['A4', 'background-color:#57BB8A;">1<', 'cell A4'],
            ['I4', 'null">&nbsp;<', 'empty cell I4'],
            ['J4', 'background-color:#E67C73;">10<', 'cell J4'],
        ];
        foreach ($expectedMatches as $expected) {
            [$coordinate, $expectedString, $message] = $expected;
            $string = $this->extractCell($coordinate);
            self::assertStringContainsString($expectedString, $string, $message);
        }
    }
}
