<?php

namespace PhpOffice\PhpSpreadsheetTests\Reader\Ods;

use PhpOffice\PhpSpreadsheet\Reader\Ods;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PHPUnit\Framework\TestCase;

class HiddenWorksheetTest extends TestCase
{
    /**
     * @var Spreadsheet
     */
    private $spreadsheet;

    protected function setup(): void
    {
        $filename = 'tests/data/Reader/Ods/HiddenSheet.ods';
        $reader = new Ods();
        $this->spreadsheet = $reader->load($filename);
    }

    public function testPageSetup(): void
    {
        $assertions = $this->worksheetAssertions();

        foreach ($this->spreadsheet->getAllSheets() as $worksheet) {
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
