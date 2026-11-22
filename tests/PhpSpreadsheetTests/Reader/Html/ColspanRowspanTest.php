<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Html;

use PhpOffice\PhpSpreadsheet\Reader\Html as HtmlReader;
use PHPUnit\Framework\TestCase;

class ColspanRowspanTest extends TestCase
{
    public function testTooLargeColspan(): void
    {
        $html = <<<EOF
            <html><body><table>
                <tr>
                    <td colspan="1234567890" style="font-family:Arial;">x</td>
                </tr>
            </table></body></html>
            EOF;
        $reader = new HtmlReader();
        $spreadsheet = $reader->loadFromString($html);
        $sheet = $spreadsheet->getActiveSheet();
        self::assertSame('A', $sheet->getHighestColumn());
        self::assertSame('x', $sheet->getCell('A1')->getValue());
        self::assertNull($sheet->getCell('B1')->getValue());
        self::assertSame(['A1:XFD1'], array_keys($sheet->getMergeCells()));
        self::assertSame('Arial', $sheet->getStyle('A1')->getFont()->getName());
        self::assertSame('Arial', $sheet->getStyle('A1:XFD1')->getFont()->getName());
        $spreadsheet->disconnectWorksheets();
    }

    public function testLargeColspan(): void
    {
        $html = <<<EOF
            <html><body><table>
                <tr>
                    <td colspan="16383" style="font-family:Arial;">x</td>
                    <td>y</td>
                </tr>
            </table></body></html>
            EOF;
        $reader = new HtmlReader();
        $spreadsheet = $reader->loadFromString($html);
        $sheet = $spreadsheet->getActiveSheet();
        self::assertSame('XFD', $sheet->getHighestColumn());
        self::assertSame('XFD', $sheet->getHighestDataColumn());
        self::assertSame('x', $sheet->getCell('A1')->getValue());
        self::assertSame('Arial', $sheet->getStyle('A1')->getFont()->getName());
        self::assertSame('y', $sheet->getCell('XFD1')->getValue());
        self::assertSame('Calibri', $sheet->getStyle('XFD1')->getFont()->getName());
        $spreadsheet->disconnectWorksheets();
    }

    public function testTooLargeRowspan(): void
    {
        $html = <<<EOF
            <html><body><table>
                <tr>
                    <td rowspan="1234567890" style="font-family:Arial;">x</td>
                </tr>
            </table></body></html>
            EOF;
        $reader = new HtmlReader();
        $spreadsheet = $reader->loadFromString($html);
        $sheet = $spreadsheet->getActiveSheet();
        self::assertSame(1, $sheet->getHighestRow());
        self::assertSame('x', $sheet->getCell('A1')->getValue());
        self::assertNull($sheet->getCell('A2')->getValue());
        self::assertSame(['A1:A1048576'], array_keys($sheet->getMergeCells()));
        self::assertSame('Arial', $sheet->getStyle('A1')->getFont()->getName());
        $spreadsheet->disconnectWorksheets();
    }

    public static function testBothTooLarge(): void
    {
        $html = <<<EOF
            <html><body><table>
                <tr>
                    <td colspan="1234567890" rowspan="1234567890" style="font-family:Arial;">x</td>
                </tr>
            </table></body></html>
            EOF;
        $reader = new HtmlReader();
        $spreadsheet = $reader->loadFromString($html);
        $sheet = $spreadsheet->getActiveSheet();
        self::assertSame('A', $sheet->getHighestColumn());
        self::assertSame('x', $sheet->getCell('A1')->getValue());
        self::assertNull($sheet->getCell('B1')->getValue());
        self::assertNull($sheet->getCell('A2')->getValue());
        self::assertSame(['A1:XFD1048576'], array_keys($sheet->getMergeCells()));
        self::assertSame('Arial', $sheet->getStyle('A1')->getFont()->getName());
        $spreadsheet->disconnectWorksheets();
    }

    public function testBothSpans(): void
    {
        $html = <<<EOF
            <html><body><table>
                <tr>
                    <td>z</td>
                    <td colspan="3" rowspan="4" style="font-family:Arial;">x</td>
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
        $expected = [
            ['z', 'x', null, null, 'y'],
            ['a2', null, null, null, 'b2'],
            ['a3', null, null, null, 'b3'],
            ['a4', null, null, null, 'b4'],
            ['a5', 'b5', null, null, null],
        ];
        self::assertSame($expected, $sheet->toArray());
        $spreadsheet->disconnectWorksheets();
    }

    public function testAdjacentRowspans(): void
    {
        $html = <<<EOF
            <html><body><table>
                <tr>
                    <td rowspan="2">A</td>
                    <td rowspan="2">B</td>
                </tr>
                <tr>
                    <td>C</td>
                </tr>
            </table></body></html>
            EOF;
        $reader = new HtmlReader();
        $spreadsheet = $reader->loadFromString($html);
        $sheet = $spreadsheet->getActiveSheet();
        self::assertSame(['A1:A2', 'B1:B2'], array_keys($sheet->getMergeCells()));
        self::assertSame('C', $sheet->getCell('C2')->getValue());
        self::assertNull($sheet->getCell('B2')->getValue());
        $spreadsheet->disconnectWorksheets();
    }
}
