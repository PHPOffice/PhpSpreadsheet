<?php

namespace PhpOffice\PhpSpreadsheetTests\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx as Reader;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class Issue2266Test extends AbstractFunctional
{
    /**
     * @dataProvider providerType
     */
    public function testIssue2266(string $type): void
    {
        // Problem deleting sheet containing local defined name.
        $reader = new Reader();
        $spreadsheet = $reader->load('tests/data/Writer/XLSX/issue.2266f.xlsx');
        self::assertCount(2, $spreadsheet->getAllSheets());
        self::assertCount(1, $spreadsheet->getDefinedNames());
        $index = 1;
        $sheet = $spreadsheet->getSheet($index);
        self::assertSame('Sheet2', $sheet->getTitle());
        $definedName = $spreadsheet->getDefinedName('LocalName', $sheet);
        self::assertNotNull($definedName);
        self::assertTrue($definedName->getLocalOnly());
        $spreadsheet->removeSheetByIndex($index);

        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, $type);
        $spreadsheet->disconnectWorksheets();

        self::assertCount(1, $reloadedSpreadsheet->getAllSheets());
        self::assertCount(0, $reloadedSpreadsheet->getDefinedNames());
        self::assertNotEquals('Sheet2', $reloadedSpreadsheet->getSheet(0)->getTitle());

        $reloadedSpreadsheet->disconnectWorksheets();
    }

    public static function providerType(): array
    {
        return [
            ['Xlsx'],
            ['Xls'],
            ['Ods'],
        ];
    }
}
