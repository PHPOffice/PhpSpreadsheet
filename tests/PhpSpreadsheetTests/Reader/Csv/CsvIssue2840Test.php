<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Csv;

use PhpOffice\PhpSpreadsheet\Reader\Csv;
use PHPUnit\Framework\TestCase;

class CsvIssue2840Test extends TestCase
{
    public function testNullStringIgnore(): void
    {
        $reader = new Csv();
        self::assertFalse($reader->getPreserveNullString());
        $inputData = <<<EOF
            john,,doe,,
            mary,,jane,,
            EOF;
        $expected = [
            ['john', null, 'doe'],
            ['mary', null, 'jane'],
        ];
        $spreadsheet = $reader->loadSpreadsheetFromString($inputData);
        $sheet = $spreadsheet->getActiveSheet();
        self::assertSame($expected, $sheet->toArray());
        $spreadsheet->disconnectWorksheets();
    }

    public function testNullStringLoad(): void
    {
        $reader = new Csv();
        $reader->setPreserveNullString(true);
        $inputData = <<<EOF
            john,,doe,,
            mary,,jane,,
            EOF;
        $expected = [
            ['john', '', 'doe', '', ''],
            ['mary', '', 'jane', '', ''],
        ];
        $spreadsheet = $reader->loadSpreadsheetFromString($inputData);
        $sheet = $spreadsheet->getActiveSheet();
        self::assertSame($expected, $sheet->toArray());
        $spreadsheet->disconnectWorksheets();
    }
}
