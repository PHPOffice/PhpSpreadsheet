<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Html;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpOffice\PhpSpreadsheet\Writer\Html as HtmlWriter;
use PHPUnit\Framework\TestCase;

class HtmlConditionalFormattingTest extends TestCase
{
    private string $data = '';

    protected function setUp(): void
    {
        $file = 'samples/templates/BasicConditionalFormatting.xlsx';
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

    public function testConditionalFormattingHtmLOutput(): void
    {
        $expectedMatches = [
            ['B1', 'class="column1 style1 s">Jan<', 'no conditional styling for B1'],
            ['F2', 'background-color:#C6EFCE;">120<', 'conditional style for F2'],
            ['H2', 'background-color:#FFEB9C;">90<', 'conditional style for H2'],
            ['F3', 'background-color:#C6EFCE;">70<', 'conditional style for cell F3'],
            ['H3', 'background-color:#FFEB9C;">60<', 'conditional style for cell H3'],
            ['F4', 'background-color:#C6EFCE;">1<', 'conditional style for cell F4'],
            ['L4', 'background-color:#FFC7CE;">5<', 'conditional style for cell L4'],
            ['F5', 'class="column5 style1 n">0<', 'no conditional styling for F5'],
        ];
        foreach ($expectedMatches as $expected) {
            [$coordinate, $expectedString, $message] = $expected;
            $string = $this->extractCell($coordinate);
            self::assertStringContainsString($expectedString, $string, $message);
        }
    }
}
