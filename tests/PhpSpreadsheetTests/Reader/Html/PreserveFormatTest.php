<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Html;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Html as HtmlWriter;
use PHPUnit\Framework\TestCase;

class PreserveFormatTest extends TestCase
{
    public function testCanApplyInlineDataFormat(): void
    {
        $html = '<table>
                    <tr>
                        <td data-format="mmm-yy">2019-02-02 12:34:00</td>
                        <td data-format="#.000">3</td>
                        <td data-format="#.000">x</td>
                        <td data-format="$#,###.00" data-value="1234">$1,234.00</td>
                    </tr>
                </table>';
        $spreadsheet = HtmlHelper::loadHtmlStringIntoSpreadsheet($html);
        $sheet = $spreadsheet->getSheet(0);

        self::assertSame('mmm-yy', $sheet->getStyle('A1')->getNumberFormat()->getFormatCode());
        self::assertSame('2019-02-02 12:34:00', $sheet->getCell('A1')->getFormattedValue(), 'field is string not number so not formatted');
        self::assertSame('#.000', $sheet->getStyle('B1')->getNumberFormat()->getFormatCode());
        self::assertSame('3.000', $sheet->getCell('B1')->getFormattedValue(), 'format applied to numeric value');
        self::assertSame('#.000', $sheet->getStyle('C1')->getNumberFormat()->getFormatCode());
        self::assertSame('x', $sheet->getCell('C1')->getFormattedValue(), 'format not applied to non-numeric value');
        self::assertSame('$#,###.00', $sheet->getStyle('D1')->getNumberFormat()->getFormatCode());
        self::assertSame('$1,234.00', $sheet->getCell('D1')->getFormattedValue());
        self::assertSame(1234, $sheet->getCell('D1')->getValue());
        $spreadsheet->disconnectWorksheets();
    }

    public static function testPreserve(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $payload = '<img src=x onerror=alert(document.domain)>';
        $formatCode = '@';
        $sheet->setCellValue('A1', $payload);
        $sheet->getStyle('A1')
            ->getNumberFormat()
            ->setFormatCode($formatCode);
        $sheet->setCellValue('A2', 3.00);
        $sheet->setCellValue('A3', 3.00);
        $sheet->getStyle('A3')
            ->getNumberFormat()
            ->setFormatCode('0.00');
        $formatCode = '@ <"items">';
        $sheet->setCellValue('B1', $payload);
        $sheet->getStyle('B1')
            ->getNumberFormat()
            ->setFormatCode($formatCode);
        $sheet->setCellValue('B2', 1234);
        $sheet->getStyle('B2')
            ->getNumberFormat()
            ->setFormatCode('$#,###.00');
        $sheet->setCellValue('B3', '=2*5');
        $sheet->getStyle('B3')
            ->getNumberFormat()
            ->setFormatCode('0.0');
        $writer = new HtmlWriter($spreadsheet);
        $writer->setPreserveFormatAndValue(true)
            ->setDataFormula(true);
        $html = $writer->generateHtmlAll();
        $spreadsheet->disconnectWorksheets();
        $expected = [
            'A1' => '<td data-type="s" data-format="@" data-value="&lt;img src=x onerror=alert(document.domain)&gt;" class="column0 style1 s">&lt;img src=x onerror=alert(document.domain)&gt;</td>',
            'B1' => '<td data-type="s" data-format="@ &lt;&quot;items&quot;&gt;" data-value="&lt;img src=x onerror=alert(document.domain)&gt;" class="column1 style3 s">&lt;img src=x onerror=alert(document.domain)&gt; &lt;items&gt;</td>',
            'A2' => '<td class="column0 style0 n">3</td>',
            'B2' => '<td data-type="n" data-format="$#,###.00" data-value="1234" class="column1 style4 n">$1,234.00</td>',
            'C1' => '<td data-type="n" data-format="0.00" data-value="3" class="column0 style2 n">3.00</td>',
            'C2' => '<td data-type="n" data-format="0.0" data-formula="=2*5" class="column1 style5 n">10.0</td>',
        ];
        foreach ($expected as $key => $value) {
            self::assertStringContainsString($value, $html, "Cell $key");
        }
        $spreadsheet2 = HtmlHelper::loadHtmlStringIntoSpreadsheet($html);
        $sheet2 = $spreadsheet2->getActiveSheet();

        self::assertSame('@', $sheet2->getStyle('A1')->getNumberFormat()->getFormatCode());
        self::assertSame($payload, $sheet2->getCell('A1')->getValue());
        self::assertSame($payload, $sheet2->getCell('A1')->getFormattedValue());
        self::assertSame('@ <"items">', $sheet2->getStyle('B1')->getNumberFormat()->getFormatCode());
        self::assertSame($payload, $sheet2->getCell('B1')->getValue());
        self::assertSame($payload . ' <items>', $sheet2->getCell('B1')->getFormattedValue());

        self::assertSame('General', $sheet2->getStyle('A2')->getNumberFormat()->getFormatCode());
        self::assertSame(3, $sheet2->getCell('A2')->getValue());
        self::assertSame('3', $sheet2->getCell('A2')->getFormattedValue());
        self::assertSame('$#,###.00', $sheet2->getStyle('B2')->getNumberFormat()->getFormatCode());
        self::assertSame(1234, $sheet2->getCell('B2')->getValue());
        self::assertSame('$1,234.00', $sheet2->getCell('B2')->getFormattedValue());

        self::assertSame('0.00', $sheet2->getStyle('A3')->getNumberFormat()->getFormatCode());
        self::assertSame(3, $sheet2->getCell('A3')->getValue());
        self::assertSame('3.00', $sheet2->getCell('A3')->getFormattedValue());
        self::assertSame('0.0', $sheet2->getStyle('B3')->getNumberFormat()->getFormatCode());
        self::assertSame('=2*5', $sheet2->getCell('B3')->getValue());
        self::assertSame('10.0', $sheet2->getCell('B3')->getFormattedValue());

        $spreadsheet2->disconnectWorksheets();
    }

    public static function testNoPreserve(): void
    {
        // Same as above, without preserveFormatAndValue
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $payload = '<img src=x onerror=alert(document.domain)>';
        $formatCode = '@';
        $sheet->setCellValue('A1', $payload);
        $sheet->getStyle('A1')
            ->getNumberFormat()
            ->setFormatCode($formatCode);
        $sheet->setCellValue('A2', 3.00);
        $sheet->setCellValue('A3', 3.00);
        $sheet->getStyle('A3')
            ->getNumberFormat()
            ->setFormatCode('0.00');
        $formatCode = '@ <"items">';
        $sheet->setCellValue('B1', $payload);
        $sheet->getStyle('B1')
            ->getNumberFormat()
            ->setFormatCode($formatCode);
        $sheet->setCellValue('B2', 1234);
        $sheet->getStyle('B2')
            ->getNumberFormat()
            ->setFormatCode('$#,###.00');
        $sheet->setCellValue('B3', '=2*5');
        $sheet->getStyle('B3')
            ->getNumberFormat()
            ->setFormatCode('0.0');
        $writer = new HtmlWriter($spreadsheet);
        $html = $writer->generateHtmlAll();
        $spreadsheet->disconnectWorksheets();
        $expected = [
            'A1' => '<td class="column0 style1 s">&lt;img src=x onerror=alert(document.domain)&gt;</td>',
            'B1' => '<td class="column1 style3 s">&lt;img src=x onerror=alert(document.domain)&gt; &lt;items&gt;</td>',
            'A2' => '<td class="column0 style0 n">3</td>',
            'B2' => '<td class="column1 style4 n">$1,234.00</td>',
            'C1' => '<td class="column0 style2 n">3.00</td>',
            'C2' => '<td class="column1 style5 n">10.0</td>',
        ];
        foreach ($expected as $key => $value) {
            self::assertStringContainsString($value, $html, "Cell $key");
        }
        $spreadsheet2 = HtmlHelper::loadHtmlStringIntoSpreadsheet($html);
        $sheet2 = $spreadsheet2->getActiveSheet();

        self::assertSame('General', $sheet2->getStyle('A1')->getNumberFormat()->getFormatCode());
        self::assertSame($payload, $sheet2->getCell('A1')->getValue());
        self::assertSame($payload, $sheet2->getCell('A1')->getFormattedValue());
        self::assertSame('General', $sheet2->getStyle('B1')->getNumberFormat()->getFormatCode());
        self::assertSame($payload . ' <items>', $sheet2->getCell('B1')->getValue());
        self::assertSame($payload . ' <items>', $sheet2->getCell('B1')->getFormattedValue());

        self::assertSame('General', $sheet2->getStyle('A2')->getNumberFormat()->getFormatCode());
        self::assertSame(3, $sheet2->getCell('A2')->getValue());
        self::assertSame('3', $sheet2->getCell('A2')->getFormattedValue());
        self::assertSame('General', $sheet2->getStyle('B2')->getNumberFormat()->getFormatCode());
        self::assertSame('$1,234.00', $sheet2->getCell('B2')->getValue());
        self::assertSame('$1,234.00', $sheet2->getCell('B2')->getFormattedValue());

        self::assertSame('General', $sheet2->getStyle('A3')->getNumberFormat()->getFormatCode());
        self::assertSame(3.00, $sheet2->getCell('A3')->getValue());
        self::assertSame('3', $sheet2->getCell('A3')->getFormattedValue());
        self::assertSame('General', $sheet2->getStyle('B3')->getNumberFormat()->getFormatCode());
        self::assertSame(10.0, $sheet2->getCell('B3')->getValue());
        self::assertSame('10', $sheet2->getCell('B3')->getFormattedValue());

        $spreadsheet2->disconnectWorksheets();
    }
}
