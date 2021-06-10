<?php

namespace PhpOffice\PhpSpreadsheetTests\Reader\Html;

use PhpOffice\PhpSpreadsheet\Reader\Exception as ReaderException;
use PhpOffice\PhpSpreadsheet\Reader\Html;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PHPUnit\Framework\TestCase;

class HtmlTest extends TestCase
{
    public function testCsvWithAngleBracket(): void
    {
        $filename = 'tests/data/Reader/HTML/csv_with_angle_bracket.csv';
        $reader = new Html();
        self::assertFalse($reader->canRead($filename));
    }

    public function testBadHtml(): void
    {
        $filename = 'tests/data/Reader/HTML/badhtml.html';
        $reader = new Html();
        self::assertTrue($reader->canRead($filename));

        $this->expectException(ReaderException::class);
        $reader->load($filename);
    }

    public function testNonHtml(): void
    {
        $filename = __FILE__;
        $reader = new Html();
        self::assertFalse($reader->canRead($filename));

        $this->expectException(ReaderException::class);
        $reader->load($filename);
    }

    public function testInvalidFilename(): void
    {
        $reader = new Html();
        self::assertEquals(0, $reader->getSheetIndex());
        self::assertFalse($reader->canRead(''));
    }

    public function providerCanReadVerySmallFile(): array
    {
        $padding = str_repeat('a', 2048);

        return [
            [true, ' <html> ' . $padding . ' </html> '],
            [true, ' <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd"> <html>' . $padding . '</html>'],
            [true, '<html></html>'],
            [false, ''],
        ];
    }

    /**
     * @dataProvider providerCanReadVerySmallFile
     *
     * @param bool $expected
     * @param string $content
     */
    public function testCanReadVerySmallFile($expected, $content): void
    {
        $filename = HtmlHelper::createHtml($content);
        $reader = new Html();
        $actual = $reader->canRead($filename);

        self::assertSame($expected, $actual);

        unlink($filename);
    }

    public function testBackgroundColorInRanding(): void
    {
        $html = '<table>
                    <tr>
                        <td style="background-color: #0000FF;color: #FFFFFF">Blue background</td>
                        <td style="background-color: unknown1;color: unknown2">Unknown fore/background</td>
                    </tr>
                </table>';
        $filename = HtmlHelper::createHtml($html);
        $spreadsheet = HtmlHelper::loadHtmlIntoSpreadsheet($filename, true);
        $firstSheet = $spreadsheet->getSheet(0);
        $style = $firstSheet->getCell('A1')->getStyle();
        self::assertEquals('FFFFFF', $style->getFont()->getColor()->getRGB());
        self::assertEquals('0000FF', $style->getFill()->getStartColor()->getRGB());
        self::assertEquals('0000FF', $style->getFill()->getEndColor()->getRGB());
        $style = $firstSheet->getCell('B1')->getStyle();
        self::assertEquals('000000', $style->getFont()->getColor()->getRGB());
        self::assertEquals('000000', $style->getFill()->getEndColor()->getRGB());
        self::assertEquals('FFFFFF', $style->getFill()->getstartColor()->getRGB());
    }

    public function testCanApplyInlineFontStyles(): void
    {
        $html = '<table>
                    <tr>
                        <td style="font-size: 16px;">16px</td>
                        <td style="font-family: \'Times New Roman\'">Times New Roman</td>
                        <td style="font-weight: bold;">Bold</td>
                        <td style="font-style: italic;">Italic</td>
                        <td style="text-decoration: underline;">Underline</td>
                        <td style="text-decoration: line-through;">Line through</td>
                    </tr>
                </table>';
        $filename = HtmlHelper::createHtml($html);
        $spreadsheet = HtmlHelper::loadHtmlIntoSpreadsheet($filename, true);
        $firstSheet = $spreadsheet->getSheet(0);

        $style = $firstSheet->getCell('A1')->getStyle();
        self::assertEquals(16, $style->getFont()->getSize());

        $style = $firstSheet->getCell('B1')->getStyle();
        self::assertEquals('Times New Roman', $style->getFont()->getName());

        $style = $firstSheet->getCell('C1')->getStyle();
        self::assertTrue($style->getFont()->getBold());

        $style = $firstSheet->getCell('D1')->getStyle();
        self::assertTrue($style->getFont()->getItalic());

        $style = $firstSheet->getCell('E1')->getStyle();
        self::assertEquals(Font::UNDERLINE_SINGLE, $style->getFont()->getUnderline());

        $style = $firstSheet->getCell('F1')->getStyle();
        self::assertTrue($style->getFont()->getStrikethrough());
    }

    public function testCanApplyInlineWidth(): void
    {
        $html = '<table>
                    <tr>
                        <td width="50">50px</td>
                        <td style="width: 100px;">100px</td>
                        <td width="50px">50px</td>
                    </tr>
                </table>';
        $filename = HtmlHelper::createHtml($html);
        $spreadsheet = HtmlHelper::loadHtmlIntoSpreadsheet($filename, true);
        $firstSheet = $spreadsheet->getSheet(0);

        $dimension = $firstSheet->getColumnDimension('A');
        self::assertNotNull($dimension);
        self::assertEquals(50, $dimension->getWidth());

        $dimension = $firstSheet->getColumnDimension('B');
        self::assertNotNull($dimension);
        self::assertEquals(100, $dimension->getWidth('px'));

        $dimension = $firstSheet->getColumnDimension('C');
        self::assertNotNull($dimension);
        self::assertEquals(50, $dimension->getWidth('px'));
    }

    public function testCanApplyInlineHeight(): void
    {
        $html = '<table>
                    <tr>
                        <td height="50">1</td>
                    </tr>
                    <tr>
                        <td style="height: 100px;">2</td>
                    </tr>
                    <tr>
                        <td height="50px">1</td>
                    </tr>
                </table>';
        $filename = HtmlHelper::createHtml($html);
        $spreadsheet = HtmlHelper::loadHtmlIntoSpreadsheet($filename, true);
        $firstSheet = $spreadsheet->getSheet(0);

        $dimension = $firstSheet->getRowDimension(1);
        self::assertNotNull($dimension);
        self::assertEquals(50, $dimension->getRowHeight());

        $dimension = $firstSheet->getRowDimension(2);
        self::assertNotNull($dimension);
        self::assertEquals(100, $dimension->getRowHeight('px'));

        $dimension = $firstSheet->getRowDimension(3);
        self::assertNotNull($dimension);
        self::assertEquals(50, $dimension->getRowHeight('px'));
    }

    public function testCanApplyAlignment(): void
    {
        $html = '<table>
                    <tr>
                        <td align="center">Center align</td>
                        <td valign="center">Center valign</td>
                        <td style="text-align: center;">Center align</td>
                        <td style="vertical-align: center;">Center valign</td>
                        <td style="text-indent: 10px;">Text indent</td>
                        <td style="word-wrap: break-word;">Wraptext</td>
                    </tr>
                </table>';
        $filename = HtmlHelper::createHtml($html);
        $spreadsheet = HtmlHelper::loadHtmlIntoSpreadsheet($filename, true);
        $firstSheet = $spreadsheet->getSheet(0);

        $style = $firstSheet->getCell('A1')->getStyle();
        self::assertEquals(Alignment::HORIZONTAL_CENTER, $style->getAlignment()->getHorizontal());

        $style = $firstSheet->getCell('B1')->getStyle();
        self::assertEquals(Alignment::VERTICAL_CENTER, $style->getAlignment()->getVertical());

        $style = $firstSheet->getCell('C1')->getStyle();
        self::assertEquals(Alignment::HORIZONTAL_CENTER, $style->getAlignment()->getHorizontal());

        $style = $firstSheet->getCell('D1')->getStyle();
        self::assertEquals(Alignment::VERTICAL_CENTER, $style->getAlignment()->getVertical());

        $style = $firstSheet->getCell('E1')->getStyle();
        self::assertEquals(10, $style->getAlignment()->getIndent());

        $style = $firstSheet->getCell('F1')->getStyle();
        self::assertTrue($style->getAlignment()->getWrapText());
    }

    public function testCanApplyInlineDataFormat(): void
    {
        $html = '<table>
                    <tr>
                        <td data-format="mmm-yy">2019-02-02 12:34:00</td>
                    </tr>
                </table>';
        $filename = HtmlHelper::createHtml($html);
        $spreadsheet = HtmlHelper::loadHtmlIntoSpreadsheet($filename, true);
        $firstSheet = $spreadsheet->getSheet(0);

        $style = $firstSheet->getCell('A1')->getStyle();
        self::assertEquals('mmm-yy', $style->getNumberFormat()->getFormatCode());
    }

    public function testCanApplyCellWrapping(): void
    {
        $html = '<table>
                    <tr>
                        <td>Hello World</td>
                    </tr>
                    <tr>
                        <td>Hello<br />World</td>
                    </tr>
                    <tr>
                        <td>Hello<br>World</td>
                    </tr>
                </table>';
        $filename = HtmlHelper::createHtml($html);
        $spreadsheet = HtmlHelper::loadHtmlIntoSpreadsheet($filename, true);
        $firstSheet = $spreadsheet->getSheet(0);

        $cellStyle = $firstSheet->getStyle('A1');
        self::assertFalse($cellStyle->getAlignment()->getWrapText());

        $cellStyle = $firstSheet->getStyle('A2');
        self::assertTrue($cellStyle->getAlignment()->getWrapText());
        $cellValue = $firstSheet->getCell('A2')->getValue();
        self::assertStringContainsString("\n", $cellValue);

        $cellStyle = $firstSheet->getStyle('A3');
        self::assertTrue($cellStyle->getAlignment()->getWrapText());
        $cellValue = $firstSheet->getCell('A3')->getValue();
        self::assertStringContainsString("\n", $cellValue);
    }

    public function testRowspanInRendering(): void
    {
        $filename = 'tests/data/Reader/HTML/rowspan.html';
        $reader = new Html();
        $spreadsheet = $reader->load($filename);

        $actual = $spreadsheet->getActiveSheet()->getMergeCells();
        self::assertSame(['A2:C2' => 'A2:C2'], $actual);
    }

    public function testTextIndentUseRowspan(): void
    {
        $html = '<table>
                  <tr>
                    <td>1</td>
                    <td rowspan="2" style="vertical-align: center;">Center Align</td>
                    <td>Row</td>
                  </tr>
                  <tr>
                    <td>2</td>
                    <td style="text-indent:10px">Text Indent</td>
                  </tr>
                </table>';
        $filename = HtmlHelper::createHtml($html);
        $spreadsheet = HtmlHelper::loadHtmlIntoSpreadsheet($filename, true);
        $firstSheet = $spreadsheet->getSheet(0);
        $style = $firstSheet->getCell('C2')->getStyle();
        self::assertEquals(10, $style->getAlignment()->getIndent());
    }

    public function testBorderWithRowspanAndColspan(): void
    {
        $html = '<table>
                    <tr>
                        <td style="border: 1px solid black;">NOT SPANNED</td>
                        <td rowspan="2" colspan="2" style="border: 1px solid black;">SPANNED</td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid black;">NOT SPANNED</td>
                    </tr>
                </table>';

        $reader = new Html();
        $spreadsheet = $reader->loadFromString($html);
        $firstSheet = $spreadsheet->getSheet(0);
        $style = $firstSheet->getStyle('B1:C2');

        $borders = $style->getBorders();

        $totalBorders = [
            $borders->getTop(),
            $borders->getLeft(),
            $borders->getBottom(),
            $borders->getRight(),
        ];

        foreach ($totalBorders as $border) {
            self::assertEquals(Border::BORDER_THIN, $border->getBorderStyle());
        }
    }
}
