<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Html;

use PhpOffice\PhpSpreadsheet\Reader\Html as HtmlReader;
use PHPUnit\Framework\TestCase;

class BorderTest extends TestCase
{
    public function testNormalBorders(): void
    {
        $html = <<<EOF
            <html><body><table>
                <tr>
                    <td>z</td>
                    <td colspan="3" rowspan="4" style="font-family:Arial; border: 1px solid red;">x</td>
                    <td>y<td>
                </tr>
                <tr>
                    <td>a2</td>
                    <td>b2</td>
                </tr>
                <tr>
                    <td>a3</td>
                    <td>b3</td>
                </tr>
                <tr>
                    <td>a4</td>
                    <td>b4</td>
                </tr>
                <tr>
                    <td>a5</td>
                    <td>b5</td>
                </tr>
            </table></body></html>
            EOF;
        $reader = new HtmlReader();
        $spreadsheet = $reader->loadFromString($html);
        $sheet = $spreadsheet->getActiveSheet();
        $mergeRanges = array_values($sheet->getMergeCells());
        self::assertSame(['B1:D4'], $mergeRanges);
        $merge = $mergeRanges[0];
        // xlsx/xls need border style applied to merged range
        $style = $sheet->getStyle($merge);
        $borders = $style->getBorders();
        self::assertSame('ff0000', $borders->getTop()->getColor()->getRgb());
        // other readers only need style applied to top left cell in merged range
        $style = $sheet->getStyle('B1');
        $borders = $style->getBorders();
        self::assertSame('ff0000', $borders->getTop()->getColor()->getRgb());
        $style = $sheet->getStyle('B2');
        $borders = $style->getBorders();
        self::assertSame('ff0000', $borders->getTop()->getColor()->getRgb(), 'style applied throughout range');
        $spreadsheet->disconnectWorksheets();
    }

    public function testTooLargeBorders(): void
    {
        $html = <<<EOF
            <html><body><table>
                <tr>
                    <td>z</td>
                    <td colspan="51" rowspan="51" style="font-family:Arial; border: 1px solid red;">x</td>
                    <td>y<td>
                </tr>
                <tr>
                    <td>a2</td>
                    <td>b2</td>
                </tr>
                <tr>
                    <td>a3</td>
                    <td>b3</td>
                </tr>
                <tr>
                    <td>a4</td>
                    <td>b4</td>
                </tr>
                <tr>
                    <td>a5</td>
                    <td>b5</td>
                </tr>
            </table></body></html>
            EOF;
        $reader = new HtmlReader();
        $spreadsheet = $reader->loadFromString($html);
        $sheet = $spreadsheet->getActiveSheet();
        $mergeRanges = array_values($sheet->getMergeCells());
        self::assertSame(['B1:AZ51'], $mergeRanges);
        $merge = $mergeRanges[0];
        // xlsx/xls need border style applied to merged range
        $style = $sheet->getStyle($merge);
        $borders = $style->getBorders();
        self::assertSame('ff0000', $borders->getTop()->getColor()->getRgb());
        // other readers only need style applied to top left cell in merged range
        $style = $sheet->getStyle('B1');
        $borders = $style->getBorders();
        self::assertSame('ff0000', $borders->getTop()->getColor()->getRgb());
        $style = $sheet->getStyle('B2');
        $borders = $style->getBorders();
        self::assertSame('000000', $borders->getTop()->getColor()->getRgb(), 'style applied only to top left cell because span is too large');
        $spreadsheet->disconnectWorksheets();
    }
}
