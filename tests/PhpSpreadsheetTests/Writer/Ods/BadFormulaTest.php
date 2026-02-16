<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Ods;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Ods;
use PhpOffice\PhpSpreadsheet\Writer\Ods\Content;
use PHPUnit\Framework\TestCase;

class BadFormulaTest extends TestCase
{
    public function testReadOrder(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getCell('A1')->setValue('=SUM()'); // too few arguments
        $sheet->getCell('A2')->setValue('=SUM(1,2,3)');
        $content = new Content(new Ods($spreadsheet));
        $xml = $content->write();
        self::assertStringContainsString(
            '<table:table-cell table:style-name="ce0" table:formula="of:=SUM()" office:value-type="string" office:string-value="=SUM()">',
            $xml,
            'exception during calculation during write'
        );
        self::assertStringContainsString(
            '<table:table-cell table:style-name="ce0" table:formula="of:=SUM(1;2;3)" office:value-type="float" office:value="6">',
            $xml,
            'calculation okay'
        );
        $spreadsheet->disconnectWorksheets();
    }
}
