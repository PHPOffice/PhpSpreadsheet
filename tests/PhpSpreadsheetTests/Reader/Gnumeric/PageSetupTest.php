<?php

namespace PhpOffice\PhpSpreadsheetTests\Reader\Gnumeric;

use PhpOffice\PhpSpreadsheet\Reader\Gnumeric;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PHPUnit\Framework\TestCase;

class PageSetupTest extends TestCase
{
    private const MARGIN_PRECISION = 0.001;

    /**
     * @var Spreadsheet
     */
    private $spreadsheet;

    protected function setup(): void
    {
        $filename = 'tests/data/Reader/Gnumeric/PageSetup.gnumeric';
        $reader = new Gnumeric();
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
            'Sheet1' => [
                'orientation' => PageSetup::ORIENTATION_PORTRAIT,
                'scale' => 75,
                'horizontalCentered' => true,
                'verticalCentered' => false,
                'pageOrder' => PageSetup::PAGEORDER_DOWN_THEN_OVER,
            ],
            'Sheet2' => [
                'orientation' => PageSetup::ORIENTATION_LANDSCAPE,
                'scale' => 100,
                'horizontalCentered' => false,
                'verticalCentered' => true,
                'pageOrder' => PageSetup::PAGEORDER_OVER_THEN_DOWN,
            ],
            'Sheet3' => [
                'orientation' => PageSetup::ORIENTATION_PORTRAIT,
                'scale' => 90,
                'horizontalCentered' => true,
                'verticalCentered' => true,
                'pageOrder' => PageSetup::PAGEORDER_DOWN_THEN_OVER,
            ],
            'Sheet4' => [
                // Default Settings
                'orientation' => PageSetup::ORIENTATION_PORTRAIT,
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
            'Sheet1' => [
                // Here the values are in inches
                'top' => 0.315,
                'header' => 0.630,
                'left' => 0.512,
                'right' => 0.512,
                'bottom' => 0.315,
                'footer' => 0.433,
            ],
            'Sheet2' => [
                // Here the values are in inches
                'top' => 0.315,
                'header' => 0.433,
                'left' => 0.709,
                'right' => 0.709,
                'bottom' => 0.315,
                'footer' => 0.433,
            ],
            'Sheet3' => [
                // Here the values are in inches
                'top' => 0.512,
                'header' => 0.433,
                'left' => 0.709,
                'right' => 0.709,
                'bottom' => 0.512,
                'footer' => 0.433,
            ],
            'Sheet4' => [
                // Default Settings (in inches)
                'top' => 0.3,
                'header' => 0.45,
                'left' => 0.7,
                'right' => 0.7,
                'bottom' => 0.3,
                'footer' => 0.45,
            ],
        ];
    }
}
