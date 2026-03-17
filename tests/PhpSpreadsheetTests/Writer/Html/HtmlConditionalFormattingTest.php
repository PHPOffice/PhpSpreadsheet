<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Html;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Conditional;
use PhpOffice\PhpSpreadsheet\Style\ConditionalFormatting\ConditionalColorScale;
use PhpOffice\PhpSpreadsheet\Style\ConditionalFormatting\ConditionalFormatValueObject;
use PhpOffice\PhpSpreadsheet\Writer\Html as HtmlWriter;
use PHPUnit\Framework\TestCase;

class HtmlConditionalFormattingTest extends TestCase
{
    private string $data = '';

    private function populateData(): void
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
        $this->populateData();
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

    public function testPercentages(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray([
            [0.0014676309754806],
            [0.0058290134726582],
            [0.00031478256190473],
            [0.0075923486102745],
            [0.0041865730298472],
        ]);
        $sheet->getStyle('A1:A5')
            ->getNumberFormat()
            ->setFormatCode('0.00%');
        $conditionals = [];
        $conditional1 = new Conditional();
        $colorscale = new ConditionalColorScale();
        $min = new ConditionalFormatValueObject('min');
        $max = new ConditionalFormatValueObject('max');
        $mid = new ConditionalFormatValueObject('percentile', 50);
        $colorscale
            ->setSqref('A1:A5', $sheet)
            ->setMinimumColor(new Color('83CCEB'))
            ->setMidpointColor(new Color('AEAEAE'))
            ->setMaximumColor(new Color('FF6CAD'))
            ->setMinimumConditionalFormatValueObject($min)
            ->setMidpointConditionalFormatValueObject($mid)
            ->setMaximumConditionalFormatValueObject($max)
            ->setScaleArray();
        $conditional1->setColorScale($colorscale)
            ->setConditionType(Conditional::CONDITION_COLORSCALE);
        $conditionals = [$conditional1];
        $sheet->getStyle('$A$1:$A$5')
            ->setConditionalStyles($conditionals);
        $writer = new HtmlWriter($spreadsheet);
        $writer->setConditionalFormatting(true);
        $html = $writer->generateHtmlAll();
        self::assertStringContainsString('<td class="column0 style1 n" style="color:#000000;background-color:#8FC3D8;">0.15%</td>', $html);
        self::assertStringContainsString('<td class="column0 style1 n" style="color:#000000;background-color:#D58EAD;">0.58%</td>', $html);
        self::assertStringContainsString('<td class="column0 style1 n" style="color:#000000;background-color:#83CCEB;">0.03%</td>', $html);
        self::assertStringContainsString('<td class="column0 style1 n" style="color:#000000;background-color:#FF6CAD;">0.76%</td>', $html);
        self::assertStringContainsString('<td class="column0 style1 n" style="color:#000000;background-color:#AEAEAE;">0.42%</td>', $html);
        $spreadsheet->disconnectWorksheets();
    }

    public function testPercentages2(): void
    {
        // fill color was being returned incorrectly
        //     because some of the colors required leading 0.
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray([
            [0.0014676309754806],
            [0.0058290134726582],
            [0.00031478256190473],
            [0.0075923486102745],
            [0.0041865730298472],
        ]);
        $sheet->getStyle('A1:A5')
            ->getNumberFormat()
            ->setFormatCode('0.00%');
        $conditionals = [];
        $conditional1 = new Conditional();
        $colorscale = new ConditionalColorScale();
        $min = new ConditionalFormatValueObject('min');
        $max = new ConditionalFormatValueObject('max');
        $mid = new ConditionalFormatValueObject('percentile', 50);
        $colorscale
            ->setSqref('A1:A5', $sheet)
            ->setMinimumColor(new Color('FF0000'))
            ->setMidpointColor(new Color('00FF00'))
            ->setMaximumColor(new Color('0000FF'))
            ->setMinimumConditionalFormatValueObject($min)
            ->setMidpointConditionalFormatValueObject($mid)
            ->setMaximumConditionalFormatValueObject($max)
            ->setScaleArray();
        $conditional1->setColorScale($colorscale)
            ->setConditionType(Conditional::CONDITION_COLORSCALE);
        $conditionals = [$conditional1];
        $sheet->getStyle('$A$1:$A$5')
            ->setConditionalStyles($conditionals);
        $writer = new HtmlWriter($spreadsheet);
        $writer->setConditionalFormatting(true);
        $html = $writer->generateHtmlAll();
        self::assertStringContainsString('<td class="column0 style1 n" style="color:#000000;background-color:#B34B00;">0.15%</td>', $html);
        self::assertStringContainsString('<td class="column0 style1 n" style="color:#000000;background-color:#00847A;">0.58%</td>', $html);
        self::assertStringContainsString('<td class="column0 style1 n" style="color:#000000;background-color:#FF0000;">0.03%</td>', $html);
        self::assertStringContainsString('<td class="column0 style1 n" style="color:#000000;background-color:#0000FF;">0.76%</td>', $html);
        self::assertStringContainsString('<td class="column0 style1 n" style="color:#000000;background-color:#00FF00;">0.42%</td>', $html);
        $spreadsheet->disconnectWorksheets();
    }
}
