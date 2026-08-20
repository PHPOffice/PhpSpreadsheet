<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Ods;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class Issue4454Test extends AbstractFunctional
{
    /**
     * Cell references and commas inside string literals must survive a round trip,
     * while everything outside of them is still converted for Ods.
     */
    public function testStringLiteralsAreNotConverted(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getCell('A1')->setValue(1);
        $sheet->getCell('B2')->setValue('world');
        $sheet->getCell('G1')->setValue('hello ');

        // Text that looks like a cell reference.
        $sheet->getCell('E1')->setValue('="THIS IS E1"');
        // The same, next to a reference that does need converting.
        $sheet->getCell('E2')->setValue('=G1&"see E1 here"');
        // A comma inside a string, which Ods separators must not touch.
        $sheet->getCell('E3')->setValue('=IF(A1>1,"yes, really","no")');
        // A doubled quote escapes a quote, so the reference stays inside the string.
        $sheet->getCell('E4')->setValue('=CONCAT("a""b E1",B2)');
        // A range, which a string literal can never interrupt.
        $sheet->getCell('E5')->setValue('=SUM(A1:A3)');

        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, 'Ods');
        $spreadsheet->disconnectWorksheets();
        $rsheet = $reloadedSpreadsheet->getActiveSheet();

        self::assertSame('="THIS IS E1"', $rsheet->getCell('E1')->getValue());
        self::assertSame('THIS IS E1', $rsheet->getCell('E1')->getCalculatedValue());

        self::assertSame('=G1&"see E1 here"', $rsheet->getCell('E2')->getValue());
        self::assertSame('hello see E1 here', $rsheet->getCell('E2')->getCalculatedValue());

        self::assertSame('=IF(A1>1,"yes, really","no")', $rsheet->getCell('E3')->getValue());
        self::assertSame('no', $rsheet->getCell('E3')->getCalculatedValue());

        self::assertSame('=CONCAT("a""b E1",B2)', $rsheet->getCell('E4')->getValue());
        self::assertSame('a"b E1world', $rsheet->getCell('E4')->getCalculatedValue());

        self::assertSame('=SUM(A1:A3)', $rsheet->getCell('E5')->getValue());
        self::assertSame(1, $rsheet->getCell('E5')->getCalculatedValue());

        $reloadedSpreadsheet->disconnectWorksheets();
    }
}
