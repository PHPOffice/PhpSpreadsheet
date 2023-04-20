<?php

namespace PhpOffice\PhpSpreadsheetTests\Reader\Html;

use PhpOffice\PhpSpreadsheet\Reader\Html;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PHPUnit\Framework\TestCase;

class HtmlBorderTest extends TestCase
{
    public function testCanApplyInlineBordersStyles(): void
    {
        $html = '<table>
                    <tr>
                        <td style="border: 1px solid #333333;">Thin border</td>
                        <td style="border-bottom: 1px dashed #333333;">Border bottom</td>
                        <td style="border-top: 1px solid #333333;">Border top</td>
                        <td style="border-left: 1px solid green;">Border left</td>
                        <td style="border-right: 1px solid #333333;">Border right</td>
                        <td style="border: none"></td>
                        <td style="border: dashed;"></td>
                        <td style="border: dotted #333333;"></td>
                    </tr>
                </table>';
        $filename = HtmlHelper::createHtml($html);
        $spreadsheet = HtmlHelper::loadHtmlIntoSpreadsheet($filename, true);
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
        self::assertEquals(Border::BORDER_DASHED, $border->getBorderStyle());
        self::assertEquals(Border::BORDER_NONE, $style->getBorders()->getTop()->getBorderStyle());

        $style = $firstSheet->getCell('C1')->getStyle();
        $border = $style->getBorders()->getTop();
        self::assertEquals('333333', $border->getColor()->getRGB());
        self::assertEquals(Border::BORDER_THIN, $border->getBorderStyle());
        self::assertEquals(Border::BORDER_NONE, $style->getBorders()->getBottom()->getBorderStyle());

        $style = $firstSheet->getCell('D1')->getStyle();
        $border = $style->getBorders()->getLeft();
        self::assertEquals('00ff00', $border->getColor()->getRGB());
        self::assertEquals(Border::BORDER_THIN, $border->getBorderStyle());
        self::assertEquals(Border::BORDER_NONE, $style->getBorders()->getBottom()->getBorderStyle());

        $style = $firstSheet->getCell('E1')->getStyle();
        $border = $style->getBorders()->getRight();
        self::assertEquals('333333', $border->getColor()->getRGB());
        self::assertEquals(Border::BORDER_THIN, $border->getBorderStyle());
        self::assertEquals(Border::BORDER_NONE, $style->getBorders()->getBottom()->getBorderStyle());

        $style = $firstSheet->getCell('F1')->getStyle();
        $borders = $style->getBorders();
        foreach ([$borders->getTop(), $borders->getBottom(), $borders->getLeft(), $borders->getRight()] as $border) {
            self::assertEquals(Border::BORDER_NONE, $border->getBorderStyle());
        }

        $style = $firstSheet->getCell('G1')->getStyle();
        $borders = $style->getBorders();
        $border = $borders->getRight();
        self::assertEquals(Border::BORDER_DASHED, $border->getBorderStyle());

        $style = $firstSheet->getCell('H1')->getStyle();
        $borders = $style->getBorders();
        $border = $borders->getRight();
        self::assertEquals(Border::BORDER_DOTTED, $border->getBorderStyle());
        self::assertEquals('333333', $border->getColor()->getRGB());
    }

    /**
     * @dataProvider providerBorderStyle
     */
    public function testBorderStyle(string $style, string $expectedResult): void
    {
        $borders = Html::getBorderMappings();
        self::assertEquals($expectedResult, $borders[$style]);
    }

    public function testBorderStyleCoverage(): void
    {
        $expected = Html::getBorderMappings();
        $covered = [];
        foreach ($expected as $key => $val) {
            $covered[$key] = 0;
        }
        $tests = $this->providerBorderStyle();
        foreach ($tests as $test) {
            $covered[$test[0]] = 1;
        }
        foreach ($covered as $key => $val) {
            self::assertEquals(1, $val, "Borderstyle $key not tested");
        }
    }

    public static function providerBorderStyle(): array
    {
        return [
            ['dash-dot', Border::BORDER_DASHDOT],
            ['dash-dot-dot', Border::BORDER_DASHDOTDOT],
            ['dashed', Border::BORDER_DASHED],
            ['dotted', Border::BORDER_DOTTED],
            ['double', Border::BORDER_DOUBLE],
            ['hair', Border::BORDER_HAIR],
            ['medium', Border::BORDER_MEDIUM],
            ['medium-dashed', Border::BORDER_MEDIUMDASHED],
            ['medium-dash-dot', Border::BORDER_MEDIUMDASHDOT],
            ['medium-dash-dot-dot', Border::BORDER_MEDIUMDASHDOTDOT],
            ['none', Border::BORDER_NONE],
            ['slant-dash-dot', Border::BORDER_SLANTDASHDOT],
            ['solid', Border::BORDER_THIN],
            ['thick', Border::BORDER_THICK],
        ];
    }
}
