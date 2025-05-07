<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PHPUnit\Framework\TestCase;

class HiddenWorksheetTest extends TestCase
{
    public function testPageSetup(): void
    {
        $filename = 'tests/data/Reader/XLSX/HiddenSheet.xlsx';
        $reader = new Xlsx();
        $spreadsheet = $reader->load($filename);
        $assertions = $this->worksheetAssertions();

        foreach ($spreadsheet->getAllSheets() as $worksheet) {
            if (!array_key_exists($worksheet->getTitle(), $assertions)) {
                continue;
            }

            $sheetAssertions = $assertions[$worksheet->getTitle()];
            foreach ($sheetAssertions as $test => $expectedResult) {
                $actualResult = $worksheet->getSheetState();
                self::assertSame(
                    $expectedResult,
                    $actualResult,
                    "Failed asserting sheet state {$expectedResult} for Worksheet '{$worksheet->getTitle()}' {$test}"
                );
            }
        }
        $spreadsheet->disconnectWorksheets();
    }

    /** @return array<array{sheetState: string}> */
    private function worksheetAssertions(): array
    {
        return [
            'Sheet1' => [
                'sheetState' => Worksheet::SHEETSTATE_VISIBLE,
            ],
            'Sheet2' => [
                'sheetState' => Worksheet::SHEETSTATE_HIDDEN,
            ],
        ];
    }

    public function testListWorksheetInfo(): void
    {
        $filename = 'tests/data/Reader/XLSX/visibility.xlsx';
        $reader = new Xlsx();
        $expected = [
            [
                'worksheetName' => 'Sheet1',
                'lastColumnLetter' => 'A',
                'lastColumnIndex' => 0,
                'totalRows' => 1,
                'totalColumns' => 1,
                'sheetState' => 'visible',
            ],
            [
                'worksheetName' => 'Sheet2',
                'lastColumnLetter' => 'A',
                'lastColumnIndex' => 0,
                'totalRows' => 1,
                'totalColumns' => 1,
                'sheetState' => 'hidden',
            ],
            [
                'worksheetName' => 'Sheet3',
                'lastColumnLetter' => 'A',
                'lastColumnIndex' => 0,
                'totalRows' => 1,
                'totalColumns' => 1,
                'sheetState' => 'visible',
            ],
            [
                'worksheetName' => 'Sheet4',
                'lastColumnLetter' => 'B',
                'lastColumnIndex' => 1,
                'totalRows' => 1,
                'totalColumns' => 2,
                'sheetState' => 'veryHidden',
            ],
        ];
        self::assertSame($expected, $reader->listWorksheetInfo($filename));
    }
}
