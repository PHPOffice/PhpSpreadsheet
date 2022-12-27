<?php

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class SheetProtectionTest extends AbstractFunctional
{
    private const FILENAME = 'tests/data/Reader/XLSX/sheetprotect.xlsx';

    public function testSheetProtection(): void
    {
        $reader = new Xlsx();
        $originalSpreadsheet = $reader->load(self::FILENAME);
        $spreadsheet = $this->writeAndReload($originalSpreadsheet, 'Xlsx');
        $originalSpreadsheet->disconnectWorksheets();
        $assertions = $this->pageSetupAssertions();

        foreach ($spreadsheet->getAllSheets() as $worksheet) {
            if (!array_key_exists($worksheet->getTitle(), $assertions)) {
                continue;
            }

            $sheetAssertions = $assertions[$worksheet->getTitle()];
            foreach ($sheetAssertions as $test => $expectedResult) {
                $testMethodName = 'get' . ucfirst($test);
                $actualResult = $worksheet->getProtection()->$testMethodName();
                self::assertSame(
                    $expectedResult,
                    $actualResult,
                    "Failed assertion for Worksheet '{$worksheet->getTitle()}' {$test}"
                );
            }
        }
        $protection0 = $spreadsheet->getSheet(0)->getProtection();
        self::assertTrue($protection0->verify('password'));
        self::assertFalse($protection0->verify('passwordx'));
        $protection1 = $spreadsheet->getSheet(1)->getProtection();
        self::assertTrue($protection1->verify('anything'), 'no password so anything works');
        $spreadsheet->disconnectWorksheets();
    }

    private function pageSetupAssertions(): array
    {
        return [
            'Sheet1' => [
                'sheet' => true,
                'autoFilter' => null,
                'formatCells' => null,
                'formatColumns' => null,
                'formatRows' => false,
                'insertColumns' => null,
                'insertHyperlinks' => null,
                'insertRows' => null,
                'deleteColumns' => false,
                'deleteRows' => null,
                'objects' => true,
                'pivotTables' => null,
                'scenarios' => null,
                'selectLockedCells' => null,
                'selectUnlockedCells' => null,
                'sort' => false,
                'algorithm' => 'SHA-512',
                'spinCount' => 100000,
            ],
            'Sheet2' => [
                'sheet' => true,
                'autoFilter' => null,
                'formatCells' => false,
                'formatColumns' => false,
                'formatRows' => false,
                'insertColumns' => false,
                'insertHyperlinks' => null,
                'insertRows' => null,
                'deleteColumns' => null,
                'deleteRows' => null,
                'objects' => true,
                'pivotTables' => null,
                'scenarios' => true,
                'selectLockedCells' => null,
                'selectUnlockedCells' => null,
                'sort' => null,
                'algorithm' => '',
                'spinCount' => 10000,
            ],
            'Sheet3' => [
                'sheet' => true,
                'autoFilter' => null,
                'formatCells' => null,
                'formatColumns' => null,
                'formatRows' => null,
                'insertColumns' => null,
                'insertHyperlinks' => false,
                'insertRows' => null,
                'deleteColumns' => null,
                'deleteRows' => null,
                'objects' => null,
                'pivotTables' => null,
                'scenarios' => true,
                'selectLockedCells' => true,
                'selectUnlockedCells' => true,
                'sort' => null,
                'algorithm' => '',
                'spinCount' => 10000,
            ],
            'Sheet4' => [
                'sheet' => null,
                'autoFilter' => null,
                'formatCells' => null,
                'formatColumns' => null,
                'formatRows' => null,
                'insertColumns' => null,
                'insertHyperlinks' => null,
                'insertRows' => null,
                'deleteColumns' => null,
                'deleteRows' => null,
                'objects' => null,
                'pivotTables' => null,
                'scenarios' => null,
                'selectLockedCells' => null,
                'selectUnlockedCells' => null,
                'sort' => null,
                'algorithm' => '',
                'spinCount' => 10000,
            ],
        ];
    }
}
