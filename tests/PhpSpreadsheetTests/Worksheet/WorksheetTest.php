<?php

namespace PhpOffice\PhpSpreadsheetTests\Worksheet;

use PhpOffice\PhpSpreadsheet\Chart\Chart;
use PhpOffice\PhpSpreadsheet\Chart\DataSeries;
use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
use PhpOffice\PhpSpreadsheet\Chart\PlotArea;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PHPUnit\Framework\TestCase;

class WorksheetTest extends TestCase
{
    public function testSetTitle()
    {
        $testTitle = str_repeat('a', 31);

        $worksheet = new Worksheet();
        $worksheet->setTitle($testTitle);
        self::assertSame($testTitle, $worksheet->getTitle());
    }

    public function setTitleInvalidProvider()
    {
        return [
            [str_repeat('a', 32), 'Maximum 31 characters allowed in sheet title.'],
            ['invalid*title', 'Invalid character found in sheet title'],
        ];
    }

    /**
     * @param string $title
     * @param string $expectMessage
     * @dataProvider setTitleInvalidProvider
     */
    public function testSetTitleInvalid($title, $expectMessage)
    {
        // First, test setting title with validation disabled -- should be successful
        $worksheet = new Worksheet();
        $worksheet->setTitle($title, true, false);

        // Next, test again with validation enabled -- this time we should fail
        $worksheet = new Worksheet();
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage($expectMessage);
        $worksheet->setTitle($title);
    }

    public function testSetTitleDuplicate()
    {
        // Create a Spreadsheet with three Worksheets (the first is created automatically)
        $spreadsheet = new Spreadsheet();
        $spreadsheet->createSheet();
        $spreadsheet->createSheet();

        // Set unique title -- should be unchanged
        $sheet = $spreadsheet->getSheet(0);
        $sheet->setTitle('Test Title');
        self::assertSame('Test Title', $sheet->getTitle());

        // Set duplicate title -- should have numeric suffix appended
        $sheet = $spreadsheet->getSheet(1);
        $sheet->setTitle('Test Title');
        self::assertSame('Test Title 1', $sheet->getTitle());

        // Set duplicate title with validation disabled -- should be unchanged
        $sheet = $spreadsheet->getSheet(2);
        $sheet->setTitle('Test Title', true, false);
        self::assertSame('Test Title', $sheet->getTitle());
    }

    public function testSetCodeName()
    {
        $testCodeName = str_repeat('a', 31);

        $worksheet = new Worksheet();
        $worksheet->setCodeName($testCodeName);
        self::assertSame($testCodeName, $worksheet->getCodeName());
    }

    public function setCodeNameInvalidProvider()
    {
        return [
            [str_repeat('a', 32), 'Maximum 31 characters allowed in sheet code name.'],
            ['invalid*code*name', 'Invalid character found in sheet code name'],
        ];
    }

    /**
     * @param string $codeName
     * @param string $expectMessage
     * @dataProvider setCodeNameInvalidProvider
     */
    public function testSetCodeNameInvalid($codeName, $expectMessage)
    {
        // First, test setting code name with validation disabled -- should be successful
        $worksheet = new Worksheet();
        $worksheet->setCodeName($codeName, false);

        // Next, test again with validation enabled -- this time we should fail
        $worksheet = new Worksheet();
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage($expectMessage);
        $worksheet->setCodeName($codeName);
    }

    public function testSetCodeNameDuplicate()
    {
        // Create a Spreadsheet with three Worksheets (the first is created automatically)
        $spreadsheet = new Spreadsheet();
        $spreadsheet->createSheet();
        $spreadsheet->createSheet();

        // Set unique code name -- should be massaged to Snake_Case
        $sheet = $spreadsheet->getSheet(0);
        $sheet->setCodeName('Test Code Name');
        self::assertSame('Test_Code_Name', $sheet->getCodeName());

        // Set duplicate code name -- should be massaged and have numeric suffix appended
        $sheet = $spreadsheet->getSheet(1);
        $sheet->setCodeName('Test Code Name');
        self::assertSame('Test_Code_Name_1', $sheet->getCodeName());

        // Set duplicate code name with validation disabled -- should be unchanged, and unmassaged
        $sheet = $spreadsheet->getSheet(2);
        $sheet->setCodeName('Test Code Name', false);
        self::assertSame('Test Code Name', $sheet->getCodeName());
    }

    public function testFreezePaneSelectedCell()
    {
        $worksheet = new Worksheet();
        $worksheet->freezePane('B2');
        self::assertSame('B2', $worksheet->getTopLeftCell());
    }

    public function extractSheetTitleProvider()
    {
        return [
            ['B2', '', '', 'B2'],
            ['testTitle!B2', 'testTitle', 'B2', 'B2'],
            ['test!Title!B2', 'test!Title', 'B2', 'B2'],
        ];
    }

    /**
     * @param string $range
     * @param string $expectTitle
     * @param string $expectCell
     * @param string $expectCell2
     * @dataProvider extractSheetTitleProvider
     */
    public function testExtractSheetTitle($range, $expectTitle, $expectCell, $expectCell2)
    {
        // only cell reference
        self::assertSame($expectCell, Worksheet::extractSheetTitle($range));
        // with title in array
        $arRange = Worksheet::extractSheetTitle($range, true);
        self::assertSame($expectTitle, $arRange[0]);
        self::assertSame($expectCell2, $arRange[1]);
    }

    public function testCopySheet()
    {
        // Create a Spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getSheet(0);
        $sheet->setTitle('TestSheet1');

        // Create a Chart
        $dataSeries = new DataSeries(
            DataSeries::TYPE_BARCHART,
            DataSeries::GROUPING_CLUSTERED,
            [],
            [
                new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, "TestSheet1!A1"),
                new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, "TestSheet1!A2"),
            ],
            [
                new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, "TestSheet1!B1"),
                new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, "TestSheet1!B2"),
            ],
            [
                new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, "TestSheet1!C1"),
                new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, "TestSheet1!C2"),
            ]
        );
        $plotArea = new PlotArea(null, [$dataSeries]);
        $chart = new Chart('Chart 1', null, null, $plotArea);
        $sheet->addChart($chart);

        // Duplicate the Worksheet
        $newSheet = $sheet->copy();
        $newSheet->setTitle('TestSheet2');
        $spreadsheet->addSheet($newSheet);

        // Chart 1 on TestSheet1 and Chart 1 on TestSheet2 should not be the same object
        $sheetOne = $spreadsheet->getSheet(0);
        $chartOne = $sheetOne->getChartByName('Chart 1');
        $sheetTwo = $spreadsheet->getSheet(1);
        $chartTwo = $sheetTwo->getChartByName('Chart 1');
        self::assertNotSame($chartOne, $chartTwo);

        // Chart 1 on TestSheet1 should be unchanged
        /** @var DataSeries $plotGroup */
        foreach ($chartOne->getPlotArea()->getPlotGroup() as $plotGroup) {
            /** @var DataSeriesValues $plotCategory */
            foreach ($plotGroup->getPlotCategories() as $plotCategory) {
                $sheetTitle = Worksheet::extractSheetTitle($plotCategory->getDataSource(), true);
                self::assertEquals('TestSheet1', $sheetTitle[0]);
            }
            /** @var DataSeriesValues $plotLabel */
            foreach ($plotGroup->getPlotLabels() as $plotLabel) {
                $sheetTitle = Worksheet::extractSheetTitle($plotLabel->getDataSource(), true);
                self::assertEquals('TestSheet1', $sheetTitle[0]);
            }
            /** @var DataSeriesValues $plotValue */
            foreach ($plotGroup->getPlotValues() as $plotValue) {
                $sheetTitle = Worksheet::extractSheetTitle($plotValue->getDataSource(), true);
                self::assertEquals('TestSheet1', $sheetTitle[0]);
            }
        }

        // Chart 1 on TestSheet2 should be changed
        /** @var DataSeries $plotGroup */
        foreach ($chartTwo->getPlotArea()->getPlotGroup() as $plotGroup) {
            /** @var DataSeriesValues $plotCategory */
            foreach ($plotGroup->getPlotCategories() as $plotCategory) {
                $sheetTitle = Worksheet::extractSheetTitle($plotCategory->getDataSource(), true);
                self::assertEquals('TestSheet2', $sheetTitle[0]);
            }
            /** @var DataSeriesValues $plotLabel */
            foreach ($plotGroup->getPlotLabels() as $plotLabel) {
                $sheetTitle = Worksheet::extractSheetTitle($plotLabel->getDataSource(), true);
                self::assertEquals('TestSheet2', $sheetTitle[0]);
            }
            /** @var DataSeriesValues $plotValue */
            foreach ($plotGroup->getPlotValues() as $plotValue) {
                $sheetTitle = Worksheet::extractSheetTitle($plotValue->getDataSource(), true);
                self::assertEquals('TestSheet2', $sheetTitle[0]);
            }
        }
    }
}
