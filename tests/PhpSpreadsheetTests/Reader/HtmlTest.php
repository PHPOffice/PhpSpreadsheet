<?php

namespace PhpOffice\PhpSpreadsheetTests\Reader;

use PhpOffice\PhpSpreadsheet\Reader\Html;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PHPUnit\Framework\TestCase;

class HtmlTest extends TestCase
{
    public function testCsvWithAngleBracket()
    {
        $filename = __DIR__ . '/../../data/Reader/HTML/csv_with_angle_bracket.csv';
        $reader = new Html();
        self::assertFalse($reader->canRead($filename));
    }

    public function providerCanReadVerySmallFile()
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
     * @param bool   $expected
     * @param string $content
     */
    public function testCanReadVerySmallFile($expected, $content)
    {
        $filename = $this->createHtml($content);
        $reader = new Html();
        $actual = $reader->canRead($filename);

        self::assertSame($expected, $actual);

        unlink($filename);
    }

    public function testBackgroundColorInRanding()
    {
        $html = '<table>
                    <tr>
                        <td style="background-color: #000000;color: #FFFFFF">Blue background</td>
                    </tr>
                </table>';
        $filename = $this->createHtml($html);
        $spreadsheet = $this->loadHtmlIntoSpreadsheet($filename);
        $firstSheet = $spreadsheet->getSheet(0);
        $style = $firstSheet->getCell('A1')->getStyle();

        self::assertEquals('FFFFFF', $style->getFont()->getColor()->getRGB());

        unlink($filename);
    }

    public function testCanApplyInlineBordersStyles()
    {
        $html = '<table>
                    <tr>
                        <td style="border: 1px solid #333333;">Thin border</td>
                        <td style="border-bottom: 1px solid #333333;">Border bottom</td>
                        <td style="border-top: 1px solid #333333;">Border top</td>
                        <td style="border-left: 1px solid #333333;">Border left</td>
                        <td style="border-right: 1px solid #333333;">Border right</td>
                    </tr>
                </table>';
        $filename = $this->createHtml($html);
        $spreadsheet = $this->loadHtmlIntoSpreadsheet($filename);
        $firstSheet = $spreadsheet->getSheet(0);
        $style = $firstSheet->getCell('A1')->getStyle();
        $borders = $style->getBorders();

        /** @var Border $border */
        foreach ([$borders->getTop(), $borders->getBottom(), $borders->getLeft(), $borders->getRight()] as $border) {
            self::assertEquals('333333', $border->getColor()->getRGB());
            self::assertEquals(Border::BORDER_THIN, $border->getBorderStyle());
        }

        $style = $firstSheet->getCell('B1')->getStyle();
        $border = $style->getBorders()->getBottom();
        self::assertEquals('333333', $border->getColor()->getRGB());
        self::assertEquals(Border::BORDER_THIN, $border->getBorderStyle());

        $style = $firstSheet->getCell('C1')->getStyle();
        $border = $style->getBorders()->getTop();
        self::assertEquals('333333', $border->getColor()->getRGB());
        self::assertEquals(Border::BORDER_THIN, $border->getBorderStyle());

        $style = $firstSheet->getCell('D1')->getStyle();
        $border = $style->getBorders()->getLeft();
        self::assertEquals('333333', $border->getColor()->getRGB());
        self::assertEquals(Border::BORDER_THIN, $border->getBorderStyle());

        $style = $firstSheet->getCell('E1')->getStyle();
        $border = $style->getBorders()->getRight();
        self::assertEquals('333333', $border->getColor()->getRGB());
        self::assertEquals(Border::BORDER_THIN, $border->getBorderStyle());

        unlink($filename);
    }

    public function testCanApplyInlineFontStyles()
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
        $filename = $this->createHtml($html);
        $spreadsheet = $this->loadHtmlIntoSpreadsheet($filename);
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

        unlink($filename);
    }

    public function testCanApplyInlineWidth()
    {
        $html = '<table>
                    <tr>
                        <td width="50">50px</td>
                        <td style="width: 100px;">100px</td>
                    </tr>
                </table>';
        $filename = $this->createHtml($html);
        $spreadsheet = $this->loadHtmlIntoSpreadsheet($filename);
        $firstSheet = $spreadsheet->getSheet(0);

        $dimension = $firstSheet->getColumnDimension('A');
        self::assertEquals(50, $dimension->getWidth());

        $dimension = $firstSheet->getColumnDimension('B');
        self::assertEquals(100, $dimension->getWidth());

        unlink($filename);
    }

    public function testCanApplyInlineHeight()
    {
        $html = '<table>
                    <tr>
                        <td height="50">1</td>
                    </tr>
                    <tr>
                        <td style="height: 100px;">2</td>
                    </tr>
                </table>';
        $filename = $this->createHtml($html);
        $spreadsheet = $this->loadHtmlIntoSpreadsheet($filename);
        $firstSheet = $spreadsheet->getSheet(0);

        $dimension = $firstSheet->getRowDimension(1);
        self::assertEquals(50, $dimension->getRowHeight());

        $dimension = $firstSheet->getRowDimension(2);
        self::assertEquals(100, $dimension->getRowHeight());

        unlink($filename);
    }

    public function testCanApplyAlignment()
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
        $filename = $this->createHtml($html);
        $spreadsheet = $this->loadHtmlIntoSpreadsheet($filename);
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

        unlink($filename);
    }

    public function testCanApplyInlineDataFormat()
    {
        $html = '<table>
                    <tr>
                        <td data-format="mmm-yy">2019-02-02 12:34:00</td>
                    </tr>
                </table>';
        $filename = $this->createHtml($html);
        $spreadsheet = $this->loadHtmlIntoSpreadsheet($filename);
        $firstSheet = $spreadsheet->getSheet(0);

        $style = $firstSheet->getCell('A1')->getStyle();
        self::assertEquals('mmm-yy', $style->getNumberFormat()->getFormatCode());

        unlink($filename);
    }

    public function testCanInsertImage()
    {
        $imagePath = realpath(__DIR__ . '/../../data/Reader/HTML/image.jpg');

        $html = '<table>
                    <tr>
                        <td><img src="' . $imagePath . '" alt=""></td>
                    </tr>
                </table>';
        $filename = $this->createHtml($html);
        $spreadsheet = $this->loadHtmlIntoSpreadsheet($filename);
        $firstSheet = $spreadsheet->getSheet(0);

        /** @var Drawing $drawing */
        $drawing = $firstSheet->getDrawingCollection()[0];
        self::assertEquals($imagePath, $drawing->getPath());
        self::assertEquals('A1', $drawing->getCoordinates());

        unlink($filename);
    }

    /**
     * @param string $html
     *
     * @return string
     */
    private function createHtml($html)
    {
        $filename = tempnam(sys_get_temp_dir(), 'html');
        file_put_contents($filename, $html);

        return $filename;
    }

    /**
     * @param $filename
     *
     * @return \PhpOffice\PhpSpreadsheet\Spreadsheet
     */
    private function loadHtmlIntoSpreadsheet($filename)
    {
        return (new Html())->load($filename);
    }
}
