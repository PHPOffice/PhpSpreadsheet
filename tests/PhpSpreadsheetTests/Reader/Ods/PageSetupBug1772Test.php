<?php

namespace PhpOffice\PhpSpreadsheetTests\Reader\Ods;

use PhpOffice\PhpSpreadsheet\Reader\Ods;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PHPUnit\Framework\TestCase;

class PageSetupBug1772Test extends TestCase
{
    private const MARGIN_PRECISION = 0.00000001;

    /**
     * @var Spreadsheet
     */
    private $spreadsheet;

    protected function setup(): void
    {
        $filename = 'tests/data/Reader/Ods/bug1772.ods';
        $reader = new Ods();
        $this->spreadsheet = $reader->load($filename);
    }

    public function testPageSetup(): void
    {
        $assertions = $this->pageSetupAssertions();

        foreach ($this->spreadsheet->getAllSheets() as $worksheet) {
            if (!array_key_exists($worksheet->getTitle(), $assertions)) {
                continue;
            }

            $sheetAssertions = $assertions[$worksheet->getTitle()];
            foreach ($sheetAssertions as $test => $expectedResult) {
                $testMethodName = 'get' . ucfirst($test);
                $actualResult = $worksheet->getPageSetup()->$testMethodName();
                self::assertSame(
                    $expectedResult,
                    $actualResult,
                    "Failed assertion for Worksheet '{$worksheet->getTitle()}' {$test}"
                );
            }
        }
    }

    public function testPageMargins(): void
    {
        $assertions = $this->pageMarginAssertions();

        foreach ($this->spreadsheet->getAllSheets() as $worksheet) {
            if (!array_key_exists($worksheet->getTitle(), $assertions)) {
                continue;
            }

            $sheetAssertions = $assertions[$worksheet->getTitle()];
            foreach ($sheetAssertions as $test => $expectedResult) {
                $testMethodName = 'get' . ucfirst($test);
                $actualResult = $worksheet->getPageMargins()->$testMethodName();
                self::assertEqualsWithDelta(
                    $expectedResult,
                    $actualResult,
                    self::MARGIN_PRECISION,
                    "Failed assertion for Worksheet '{$worksheet->getTitle()}' {$test} margin"
                );
            }
        }
    }

    private function pageSetupAssertions(): array
    {
        return [
            'Employee update template' => [
                'orientation' => PageSetup::ORIENTATION_DEFAULT,
                'scale' => 100,
                'horizontalCentered' => false,
                'verticalCentered' => false,
                'pageOrder' => PageSetup::PAGEORDER_DOWN_THEN_OVER,
            ],
        ];
    }

    private function pageMarginAssertions(): array
    {
        return [
            'Employee update template' => [
                // Here the values are in cm
                'top' => 0.0,
                'header' => 0.2953,
                'left' => 0.0,
                'right' => 0.0,
                'bottom' => 0.0,
                'footer' => 0.2953,
            ],
        ];
    }
}
