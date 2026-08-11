<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Xls;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class Issue4331Test extends AbstractFunctional
{
    public function testIssue4331(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $c3 = '=VLOOKUP(B3,$B$10:$C$13,2,FALSE)';
        $d3 = '=VLOOKUP("intermediate",$B$10:$C$13,2,TRUE)';
        $c4 = '=VLOOKUP(B3,$B$10:$C$13,2,FALSE())';
        $d4 = '=VLOOKUP("intermediate",$B$10:$C$13,2,TRUE())';
        $sheet->fromArray(
            [
                ['level', 'result'],
                ['medium', $c3, $d3],
                [null, $c4, $d4],
            ],
            null,
            'B2',
            true
        );
        $sheet->fromArray(
            [
                ['high', 6],
                ['low', 2],
                ['medium', 4],
                ['none', 0],
            ],
            null,
            'B10',
            true
        );

        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, 'Xls');
        $spreadsheet->disconnectWorksheets();

        $worksheet = $reloadedSpreadsheet->getActiveSheet();
        self::assertSame($c3, $worksheet->getCell('C3')->getValue());
        self::assertSame(4, $worksheet->getCell('C3')->getCalculatedValue());
        self::assertSame($d3, $worksheet->getCell('D3')->getValue());
        self::assertSame(6, $worksheet->getCell('D3')->getCalculatedValue());
        self::assertSame($c4, $worksheet->getCell('C4')->getValue());
        self::assertSame(4, $worksheet->getCell('C4')->getCalculatedValue());
        self::assertSame($d4, $worksheet->getCell('D4')->getValue());
        self::assertSame(6, $worksheet->getCell('D4')->getCalculatedValue());
        $reloadedSpreadsheet->disconnectWorksheets();
    }
}
