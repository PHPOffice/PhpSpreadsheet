<?php

namespace PhpOffice\PhpSpreadsheetTests\Reader\Html;

use PhpOffice\PhpSpreadsheet\Reader\Html;
use PHPUnit\Framework\TestCase;

class HtmlLoadNonUtf8Charset extends TestCase
{
    public function testLoadNonUtf8Charset(): void
    {
        $iso_8859_1 = mb_convert_encoding('£', 'ISO-8859-1', 'UTF-8');
        $content = <<<EOF
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset="iso-8859-1">
            </head>
            <body>
              <table>
                <tr>
                  <td>Date</td>
                  <td>Description</td>
                  <td>Money in</td>
                  <td>Money Out</td>
                  <td>Balance</td>
                </tr>
                <tr>
                  <td>2024-01-14</td>
                  <td>PHPOffice/PhpSpreadSheet release</td>
                  <td></td>
                  <td>{$iso_8859_1}75.00</td>
                  <td>{$iso_8859_1}999925.00</td>
                </tr>
                <tr>
                  <td>2024-01-24</td>
                  <td>PHPOffice/PhpSpreadSheet release</td>
                  <td>{$iso_8859_1}75.00</td>
                  <td></td>
                  <td>{$iso_8859_1}1000000.00</td>
                </tr>
              </table>
            </body>
            </html>
            EOF;
        $reader = new Html();
        $spreadsheet = $reader->loadFromString($content);
        $sheet = $spreadsheet->getActiveSheet();
        self::assertSame('Date', $sheet->getCell('A1')->getValue());
        self::assertSame('Description', $sheet->getCell('B1')->getValue());
        self::assertSame('PHPOffice/PhpSpreadSheet release', $sheet->getCell('B3')->getValue());
        self::assertSame('£75.00', $sheet->getCell('D2')->getValue());
        self::assertSame('£75.00', $sheet->getCell('C3')->getValue());
    }

    public function testLoadNonUtf8CharsetHttpEquiv(): void
    {
        $iso_8859_1 = mb_convert_encoding('£', 'ISO-8859-1', 'UTF-8');
        $content = <<<EOF
            <!DOCTYPE html>
            <html>
            <head>
                <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
            </head>
            <body>
              <table>
                <tr>
                  <td>Date</td>
                  <td>Description</td>
                  <td>Money in</td>
                  <td>Money Out</td>
                  <td>Balance</td>
                </tr>
                <tr>
                  <td>2024-01-14</td>
                  <td>PHPOffice/PhpSpreadSheet release</td>
                  <td></td>
                  <td>{$iso_8859_1}75.00</td>
                  <td>{$iso_8859_1}999925.00</td>
                </tr>
                <tr>
                  <td>2024-01-24</td>
                  <td>PHPOffice/PhpSpreadSheet release</td>
                  <td>{$iso_8859_1}75.00</td>
                  <td></td>
                  <td>{$iso_8859_1}1000000.00</td>
                </tr>
              </table>
            </body>
            </html>
            EOF;
        $reader = new Html();
        $spreadsheet = $reader->loadFromString($content);
        $sheet = $spreadsheet->getActiveSheet();
        self::assertSame('Date', $sheet->getCell('A1')->getValue());
        self::assertSame('Description', $sheet->getCell('B1')->getValue());
        self::assertSame('PHPOffice/PhpSpreadSheet release', $sheet->getCell('B3')->getValue());
        self::assertSame('£75.00', $sheet->getCell('D2')->getValue());
        self::assertSame('£75.00', $sheet->getCell('C3')->getValue());
    }
}
