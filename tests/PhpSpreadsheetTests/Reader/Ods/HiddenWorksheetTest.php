<?php

namespace PhpOffice\PhpSpreadsheetTests\Reader\Ods;

use PhpOffice\PhpSpreadsheet\Reader\Ods;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PHPUnit\Framework\TestCase;

class HiddenWorksheetTest extends TestCase
{
    public function testPageSetup(): void
    {
        $filename = 'tests/data/Reader/Ods/HiddenSheet.ods';
        $reader = new Ods();
        $spreadsheet = $reader->load($filename);
        $assertions = $this->worksheetAssertions();

        $sheetCount = 0;
        foreach ($spreadsheet->getAllSheets() as $worksheet) {
            if (!array_key_exists($worksheet->getTitle(), $assertions)) {
                self::fail('Unexpected worksheet ' . $worksheet->getTitle());
            }

            ++$sheetCount;
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
        self::assertCount($sheetCount, $assertions);
        $spreadsheet->disconnectWorksheets();
    }

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
}
