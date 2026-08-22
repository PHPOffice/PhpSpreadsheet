<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Html;

use PhpOffice\PhpSpreadsheet\Reader\Html;
use PHPUnit\Framework\TestCase;

class Issue2810Test extends TestCase
{
    // Reader has been converting falsey values to null
    public function testIssue2810(): void
    {
        $content = <<<'EOF'
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset='utf-8'>
                <title>Declaracion en Linea</title>
            </head>
            <body>
              <table>
                <tr>
                  <td>1</td>
                  <td>0</td>
                  <td>2</td>
                </tr>
              </table>
            </body>
            </html>

            EOF;
        $reader = new Html();
        $spreadsheet = $reader->loadFromString($content);
        $sheet = $spreadsheet->getActiveSheet();
        self::assertSame(1, $sheet->getCell('A1')->getValue());
        self::assertSame(0, $sheet->getCell('B1')->getValue());
        self::assertSame(2, $sheet->getCell('C1')->getValue());
        $spreadsheet->disconnectWorksheets();
    }
}
