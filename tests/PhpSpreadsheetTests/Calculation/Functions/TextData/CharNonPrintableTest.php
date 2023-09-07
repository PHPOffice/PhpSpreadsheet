<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\TextData;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class CharNonPrintableTest extends AbstractFunctional
{
    /**
     * @dataProvider providerType
     */
    public function testNotPrintable(string $type): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getCell('B1')->setValue('=CHAR(2)');
        $sheet->getCell('C1')->setValue('=CHAR(127)');
        $hello = "hello\nthere";
        $sheet->getCell('D2')->setValue($hello);
        $sheet->getCell('D1')->setValue('=D2');
        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, $type);
        $result = $reloadedSpreadsheet->getActiveSheet()->getCell('B1')->getCalculatedValue();
        self::assertEquals("\x02", $result);
        $result = $reloadedSpreadsheet->getActiveSheet()->getCell('C1')->getCalculatedValue();
        self::assertEquals("\x7f", $result);
        $result = $reloadedSpreadsheet->getActiveSheet()->getCell('D1')->getCalculatedValue();
        self::assertEquals($hello, $result);
        $spreadsheet->disconnectWorksheets();
        $reloadedSpreadsheet->disconnectWorksheets();
    }

    public static function providerType(): array
    {
        return [
            ['Xlsx'],
            ['Xls'],
            //['Ods'], // no support yet in Reader or Writer
            // without csv suffix, Reader/Csv decides type via mime_get_type,
            //   and control character makes it guess application/octet-stream,
            //   so Reader/Csv decides it can't read it.
            //['Csv'],
            // DOMDocument.loadHTML() rejects '&#2;' even though legal html.
            //['Html'],
        ];
    }
}
