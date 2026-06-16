<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Html;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpOffice\PhpSpreadsheet\Writer\Html as HtmlWriter;
use PHPUnit\Framework\TestCase;

class HtmlDifferentConditionalFormattingsTest extends TestCase
{
    private string $data = '';

    protected function setUp(): void
    {
        $file = 'samples/templates/ConditionalFormattingConditions.xlsx';
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

    public function testConditionalFormattingRulesHtml(): void
    {
        $expectedMatches = [
            ['A1', 'background-color:#B7E1CD;">1<', 'A1 equals hit'],
            ['B1', 'class="column1 style1 n">2<', 'B1 equals miss'],
            ['E1', 'background-color:#B7E1CD;">1<', 'E1 equals horizontal reference hit'],
            ['F1', 'class="column5 style1 n">2<', 'F1 equals horizontal reference miss'],
            ['G1', 'class="column6 style1 n">3<', 'G1 equals horizontal reference miss'],
            ['A2', 'background-color:#B7E1CD;">terve<', 'A2 text contains hit'],
            ['B2', 'class="column1 style1 s">moi<', 'B2 text contains miss'],
            ['A3', 'background-color:#B7E1CD;">terve<', 'A3 text does not contain hit'],
            ['B3', 'class="column1 style1 s">moi<', 'B2 text does not contain miss'],
            ['A4', 'background-color:#B7E1CD;">terve<', 'A4 text starts with hit'],
            ['B4', 'class="column1 style1 s">moi<', 'B2 text starts with miss'],
            ['A5', 'background-color:#B7E1CD;">terve<', 'A5 text ends with hit'],
            ['B5', 'class="column1 style1 s">moi<', 'B5 text ends with miss'],
            ['A6', 'background-color:#B7E1CD;">2025/01/01<', 'A6 date after hit'],
            ['B6', 'class="column1 style2 n">2020/01/01<', 'B6 date after miss'],
            ['A7', 'background-color:#B7E1CD;">terve vaan<', 'A7 text contains hit'],
            ['B7', 'class="column1 style1 s">moi<', 'B7 text contains miss'],
            ['A8', 'background-color:#B7E1CD;">terve<', 'A8 text does not contain hit'],
            ['B8', 'class="column1 style1 s">terve vaan<', 'B2 does not contain miss'],
            ['A9', 'background-color:#B7E1CD;">#DIV/0!<', 'A10 own formula is error hit'],
            ['B9', 'class="column1 style1 s">moi<', 'B9 own formula is error miss'],
            ['A10', 'background-color:#B7E1CD;">moi<', 'A10 own formula is not error hit'],
            ['B10', 'class="column1 style3 s">#DIV/0!<', 'B10 own formula is not error miss'],
            ['A11', 'background-color:#B7E1CD;">terve<', 'A11 own formula count instances of cell on line and hit when more than one hit'],
            ['B11', 'background-color:#B7E1CD;">terve<', 'B11 own formula count instances of cell on line and hit when more than one hit'],
            ['C11', 'class="column2 style1 s">moi<', 'C11 own formula count instances of cell on line and hit when more than one miss'],
            ['A12', 'background-color:#B7E1CD;">moi<', 'A12 own formula count instances of cell on line and hit when at most 1 hit'],
            ['B12', 'class="column1 style1 s">terve<', 'B12 own formula count instances of cell on line and hit when at most 1 miss'],
            ['C12', 'class="column2 style1 s">terve<', 'C11 own formula count instances of cell on line and hit when at most 1 miss'],
            ['A13', 'background-color:#B7E1CD;">12<', 'A13 own formula self reference hit'],
            ['B13', 'class="column1 style1 n">10<', 'B13 own formula self reference miss'],
            ['A14', 'background-color:#B7E1CD;">10<', 'A14 multiple conditional hits'],
            ['B14', 'class="column1 style1 n">1<', 'B14 multiple conditionals miss'],
            ['F7', 'background-color:#B7E1CD;">1<', 'F7 equals vertical reference hit'],
            ['F8', 'class="column5 style1 n">2<', 'F8 equals vertical reference miss'],
            ['F9', 'class="column5 style1 n">3<', 'F9 equals vertical reference miss'],
            ['F10', 'class="column5 style1 n">4<', 'F10 equals vertical reference miss'],
        ];
        foreach ($expectedMatches as $expected) {
            [$coordinate, $expectedString, $message] = $expected;
            $string = $this->extractCell($coordinate);
            self::assertStringContainsString($expectedString, $string, $message);
        }
    }
}
