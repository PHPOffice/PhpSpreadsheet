<?php

namespace PhpOffice\PhpSpreadsheetTests\Worksheet;

use PhpOffice\PhpSpreadsheet\Chart\Chart;
use PhpOffice\PhpSpreadsheet\Chart\DataSeries;
use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
use PhpOffice\PhpSpreadsheet\Chart\Legend;
use PhpOffice\PhpSpreadsheet\Chart\PlotArea;
use PhpOffice\PhpSpreadsheet\Chart\Title;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PHPUnit\Framework\TestCase;

class ChartsAndRenamedSheetsTest extends TestCase
{
    public function testRenameSheet(): void
    {
        // Create a Spreadsheet
        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $worksheetName = 'Worksheet';
        $worksheet->setTitle($worksheetName);
        $worksheet->fromArray(
            [
                ['', 2010, 2011, 2012],
                ['Q1', 12, 15, 21],
                ['Q2', 56, 73, 86],
                ['Q3', 52, 61, 69],
                ['Q4', 30, 32, 0],
            ]
        );

        $testSheet1 = $spreadsheet->createSheet();
        $testSheet1Name = 'TestSheet1';
        $testSheet1->setTitle($testSheet1Name);
        // Create a Chart
        $dataSeriesLabels = [
            new DataSeriesValues(
                DataSeriesValues::DATASERIES_TYPE_STRING,
                $worksheetName . '!$B$1',
                null,
                1
            ), // 2010
            new DataSeriesValues(
                DataSeriesValues::DATASERIES_TYPE_STRING,
                $worksheetName . '!$C$1',
                null,
                1
            ), // 2011
            new DataSeriesValues(
                DataSeriesValues::DATASERIES_TYPE_STRING,
                $worksheetName . '!$D$1',
                null,
                1
            ), // 2012
        ];
        $xAxisTickValues = [
            new DataSeriesValues(
                DataSeriesValues::DATASERIES_TYPE_STRING,
                $worksheetName . '!$A$2:$A$5',
                null,
                4
            ), // Q1 to Q4
        ];
        $dataSeriesValues = [
            new DataSeriesValues(
                DataSeriesValues::DATASERIES_TYPE_NUMBER,
                $worksheetName . '!$B$2:$B$5',
                null,
                4
            ),
            new DataSeriesValues(
                DataSeriesValues::DATASERIES_TYPE_NUMBER,
                $worksheetName . '!$C$2:$C$5',
                null,
                4
            ),
            new DataSeriesValues(
                DataSeriesValues::DATASERIES_TYPE_NUMBER,
                $worksheetName . '!$D$2:$D$5',
                null,
                4
            ),
        ];
        $series = new DataSeries(
            DataSeries::TYPE_BARCHART, // plotType
            DataSeries::GROUPING_STACKED, // plotGrouping
            range(0, count($dataSeriesValues) - 1), // plotOrder
            $dataSeriesLabels, // plotLabel
            $xAxisTickValues, // plotCategory
            $dataSeriesValues        // plotValues
        );
        $series->setPlotDirection(DataSeries::DIRECTION_BAR);
        $plotArea = new PlotArea(null, [$series]);
        $legend = new Legend(Legend::POSITION_RIGHT, null, false);
        $title = new Title('Test Chart');
        $yAxisLabel = new Title('Value ($k)');
        $chartName = 'Chart 1';
        $chart = new Chart(
            $chartName, // name
            $title, // title
            $legend, // legend
            $plotArea, // plotArea
            true, // plotVisibleOnly
            DataSeries::EMPTY_AS_GAP, // displayBlanksAs
            null, // xAxisLabel
            $yAxisLabel  // yAxisLabel
        );
        $chart->setTopLeftPosition('A7');
        $chart->setBottomRightPosition('H20');
        $testSheet1->addChart($chart);

        // Duplicate the Worksheet
        $testSheet2 = $spreadsheet->duplicateWorksheetByTitle($testSheet1Name);
        $testSheet2Name = 'TestSheet2';
        $testSheet2->setTitle($testSheet2Name);

        $newName = 'Renamed';
        $worksheet->setTitle($newName);

        // Chart 1 on TestSheet1 and Chart 1 on TestSheet2 should not be the same object
        $chartOne = $testSheet1->getChartByNameOrThrow($chartName);
        $chartTwo = $testSheet2->getChartByNameOrThrow($chartName);
        self::assertNotSame($chartOne, $chartTwo);

        $assertion1 = $assertion2 = $assertion3 = false;
        foreach (($chartOne->getPlotArea()?->getPlotGroup() ?? []) as $plotGroup) {
            foreach ($plotGroup->getPlotCategories() as $plotCategory) {
                $sheetTitle = Worksheet::extractSheetTitle($plotCategory->getDataSource(), true, true);
                self::assertSame($newName, $sheetTitle[0]);
                $assertion1 = true;
            }
            foreach ($plotGroup->getPlotLabels() as $plotLabel) {
                $sheetTitle = Worksheet::extractSheetTitle($plotLabel->getDataSource(), true, true);
                self::assertSame($newName, $sheetTitle[0]);
                $assertion2 = true;
            }
            foreach ($plotGroup->getPlotValues() as $plotValue) {
                $sheetTitle = Worksheet::extractSheetTitle($plotValue->getDataSource(), true, true);
                self::assertSame($newName, $sheetTitle[0]);
                $assertion3 = true;
            }
        }
        self::assertTrue($assertion1);
        self::assertTrue($assertion2);
        self::assertTrue($assertion3);

        $assertion1 = $assertion2 = $assertion3 = false;
        foreach (($chartTwo->getPlotArea()?->getPlotGroup() ?? []) as $plotGroup) {
            foreach ($plotGroup->getPlotCategories() as $plotCategory) {
                $sheetTitle = Worksheet::extractSheetTitle($plotCategory->getDataSource(), true, true);
                self::assertSame($newName, $sheetTitle[0]);
                $assertion1 = true;
            }
            foreach ($plotGroup->getPlotLabels() as $plotLabel) {
                $sheetTitle = Worksheet::extractSheetTitle($plotLabel->getDataSource(), true, true);
                self::assertSame($newName, $sheetTitle[0]);
                $assertion2 = true;
            }
            foreach ($plotGroup->getPlotValues() as $plotValue) {
                $sheetTitle = Worksheet::extractSheetTitle($plotValue->getDataSource(), true, true);
                self::assertSame($newName, $sheetTitle[0]);
                $assertion3 = true;
            }
        }
        self::assertTrue($assertion1);
        self::assertTrue($assertion2);
        self::assertTrue($assertion3);
        $spreadsheet->disconnectWorksheets();
    }

    public function testSingleSheet(): void
    {
        // Create a Spreadsheet
        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $worksheetName = 'Worksheet';
        $worksheet->setTitle($worksheetName);
        $worksheet->fromArray(
            [
                ['', 2010, 2011, 2012],
                ['Q1', 12, 15, 21],
                ['Q2', 56, 73, 86],
                ['Q3', 52, 61, 69],
                ['Q4', 30, 32, 0],
            ]
        );

        // Create a Chart
        $dataSeriesLabels = [
            new DataSeriesValues(
                DataSeriesValues::DATASERIES_TYPE_STRING,
                $worksheetName . '!$B$1',
                null,
                1
            ), // 2010
            new DataSeriesValues(
                DataSeriesValues::DATASERIES_TYPE_STRING,
                $worksheetName . '!$C$1',
                null,
                1
            ), // 2011
            new DataSeriesValues(
                DataSeriesValues::DATASERIES_TYPE_STRING,
                $worksheetName . '!$D$1',
                null,
                1
            ), // 2012
        ];
        $xAxisTickValues = [
            new DataSeriesValues(
                DataSeriesValues::DATASERIES_TYPE_STRING,
                $worksheetName . '!$A$2:$A$5',
                null,
                4
            ), // Q1 to Q4
        ];
        $dataSeriesValues = [
            new DataSeriesValues(
                DataSeriesValues::DATASERIES_TYPE_NUMBER,
                $worksheetName . '!$B$2:$B$5',
                null,
                4
            ),
            new DataSeriesValues(
                DataSeriesValues::DATASERIES_TYPE_NUMBER,
                $worksheetName . '!$C$2:$C$5',
                null,
                4
            ),
            new DataSeriesValues(
                DataSeriesValues::DATASERIES_TYPE_NUMBER,
                $worksheetName . '!$D$2:$D$5',
                null,
                4
            ),
        ];
        $series = new DataSeries(
            DataSeries::TYPE_BARCHART, // plotType
            DataSeries::GROUPING_STACKED, // plotGrouping
            range(0, count($dataSeriesValues) - 1), // plotOrder
            $dataSeriesLabels, // plotLabel
            $xAxisTickValues, // plotCategory
            $dataSeriesValues        // plotValues
        );
        $series->setPlotDirection(DataSeries::DIRECTION_BAR);
        $plotArea = new PlotArea(null, [$series]);
        $legend = new Legend(Legend::POSITION_RIGHT, null, false);
        $title = new Title('Test Chart');
        $yAxisLabel = new Title('Value ($k)');
        $chartName = 'Chart 1';
        $chart = new Chart(
            $chartName, // name
            $title, // title
            $legend, // legend
            $plotArea, // plotArea
            true, // plotVisibleOnly
            DataSeries::EMPTY_AS_GAP, // displayBlanksAs
            null, // xAxisLabel
            $yAxisLabel  // yAxisLabel
        );
        $chart->setTopLeftPosition('A7');
        $chart->setBottomRightPosition('H20');
        $worksheet->addChart($chart);

        $newName = 'New Name';
        $worksheet->setTitle($newName);

        $chartOne = $worksheet->getChartByNameOrThrow($chartName);

        $assertion1 = $assertion2 = $assertion3 = false;
        foreach (($chartOne->getPlotArea()?->getPlotGroup() ?? []) as $plotGroup) {
            foreach ($plotGroup->getPlotCategories() as $plotCategory) {
                $sheetTitle = Worksheet::extractSheetTitle($plotCategory->getDataSource(), true, true);
                self::assertSame($newName, $sheetTitle[0]);
                $assertion1 = true;
            }
            foreach ($plotGroup->getPlotLabels() as $plotLabel) {
                $sheetTitle = Worksheet::extractSheetTitle($plotLabel->getDataSource(), true, true);
                self::assertSame($newName, $sheetTitle[0]);
                $assertion2 = true;
            }
            foreach ($plotGroup->getPlotValues() as $plotValue) {
                $sheetTitle = Worksheet::extractSheetTitle($plotValue->getDataSource(), true, true);
                self::assertSame($newName, $sheetTitle[0]);
                $assertion3 = true;
            }
        }
        self::assertTrue($assertion1);
        self::assertTrue($assertion2);
        self::assertTrue($assertion3);

        $spreadsheet->disconnectWorksheets();
    }

    public function testCloneSheet(): void
    {
        // Create a Spreadsheet
        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $worksheetName = 'Worksheet';
        $worksheet->setTitle($worksheetName);
        $worksheet->fromArray(
            [
                ['', 2010, 2011, 2012],
                ['Q1', 12, 15, 21],
                ['Q2', 56, 73, 86],
                ['Q3', 52, 61, 69],
                ['Q4', 30, 32, 0],
            ]
        );

        $testSheet1 = $worksheet;
        $testSheet1Name = $worksheetName;
        // Create a Chart
        $dataSeriesLabels = [
            new DataSeriesValues(
                DataSeriesValues::DATASERIES_TYPE_STRING,
                $worksheetName . '!$B$1',
                null,
                1
            ), // 2010
            new DataSeriesValues(
                DataSeriesValues::DATASERIES_TYPE_STRING,
                $worksheetName . '!$C$1',
                null,
                1
            ), // 2011
            new DataSeriesValues(
                DataSeriesValues::DATASERIES_TYPE_STRING,
                $worksheetName . '!$D$1',
                null,
                1
            ), // 2012
        ];
        $xAxisTickValues = [
            new DataSeriesValues(
                DataSeriesValues::DATASERIES_TYPE_STRING,
                $worksheetName . '!$A$2:$A$5',
                null,
                4
            ), // Q1 to Q4
        ];
        $dataSeriesValues = [
            new DataSeriesValues(
                DataSeriesValues::DATASERIES_TYPE_NUMBER,
                $worksheetName . '!$B$2:$B$5',
                null,
                4
            ),
            new DataSeriesValues(
                DataSeriesValues::DATASERIES_TYPE_NUMBER,
                $worksheetName . '!$C$2:$C$5',
                null,
                4
            ),
            new DataSeriesValues(
                DataSeriesValues::DATASERIES_TYPE_NUMBER,
                $worksheetName . '!$D$2:$D$5',
                null,
                4
            ),
        ];
        $series = new DataSeries(
            DataSeries::TYPE_BARCHART, // plotType
            DataSeries::GROUPING_STACKED, // plotGrouping
            range(0, count($dataSeriesValues) - 1), // plotOrder
            $dataSeriesLabels, // plotLabel
            $xAxisTickValues, // plotCategory
            $dataSeriesValues        // plotValues
        );
        $series->setPlotDirection(DataSeries::DIRECTION_BAR);
        $plotArea = new PlotArea(null, [$series]);
        $legend = new Legend(Legend::POSITION_RIGHT, null, false);
        $title = new Title('Test Chart');
        $yAxisLabel = new Title('Value ($k)');
        $chartName = 'Chart 1';
        $chart = new Chart(
            $chartName, // name
            $title, // title
            $legend, // legend
            $plotArea, // plotArea
            true, // plotVisibleOnly
            DataSeries::EMPTY_AS_GAP, // displayBlanksAs
            null, // xAxisLabel
            $yAxisLabel  // yAxisLabel
        );
        $chart->setTopLeftPosition('A7');
        $chart->setBottomRightPosition('H20');
        $testSheet1->addChart($chart);

        // Duplicate the Worksheet
        $testSheet2 = $spreadsheet->duplicateWorksheetByTitle($testSheet1Name);
        $testSheet2Name = 'TestSheet2';
        $testSheet2->setTitle($testSheet2Name);

        $newName = 'Renamed';
        $worksheet->setTitle($newName);

        // Chart 1 on TestSheet1 and Chart 1 on TestSheet2 should not be the same object
        $chartOne = $testSheet1->getChartByNameOrThrow($chartName);
        $chartTwo = $testSheet2->getChartByNameOrThrow($chartName);
        self::assertNotSame($chartOne, $chartTwo);

        $assertion1 = $assertion2 = $assertion3 = false;
        foreach (($chartOne->getPlotArea()?->getPlotGroup() ?? []) as $plotGroup) {
            foreach ($plotGroup->getPlotCategories() as $plotCategory) {
                $sheetTitle = Worksheet::extractSheetTitle($plotCategory->getDataSource(), true, true);
                self::assertSame($newName, $sheetTitle[0]);
                $assertion1 = true;
            }
            foreach ($plotGroup->getPlotLabels() as $plotLabel) {
                $sheetTitle = Worksheet::extractSheetTitle($plotLabel->getDataSource(), true, true);
                self::assertSame($newName, $sheetTitle[0]);
                $assertion2 = true;
            }
            foreach ($plotGroup->getPlotValues() as $plotValue) {
                $sheetTitle = Worksheet::extractSheetTitle($plotValue->getDataSource(), true, true);
                self::assertSame($newName, $sheetTitle[0]);
                $assertion3 = true;
            }
        }
        self::assertTrue($assertion1);
        self::assertTrue($assertion2);
        self::assertTrue($assertion3);

        $assertion1 = $assertion2 = $assertion3 = false;
        // Chart 2 sheetnames should point to sheet2 so should remain unchanged
        foreach (($chartTwo->getPlotArea()?->getPlotGroup() ?? []) as $plotGroup) {
            foreach ($plotGroup->getPlotCategories() as $plotCategory) {
                $sheetTitle = Worksheet::extractSheetTitle($plotCategory->getDataSource(), true, true);
                self::assertSame($testSheet2Name, $sheetTitle[0]);
                $assertion1 = true;
            }
            foreach ($plotGroup->getPlotLabels() as $plotLabel) {
                $sheetTitle = Worksheet::extractSheetTitle($plotLabel->getDataSource(), true, true);
                self::assertSame($testSheet2Name, $sheetTitle[0]);
                $assertion2 = true;
            }
            foreach ($plotGroup->getPlotValues() as $plotValue) {
                $sheetTitle = Worksheet::extractSheetTitle($plotValue->getDataSource(), true, true);
                self::assertSame($testSheet2Name, $sheetTitle[0]);
                $assertion3 = true;
            }
        }
        self::assertTrue($assertion1);
        self::assertTrue($assertion2);
        self::assertTrue($assertion3);
        $spreadsheet->disconnectWorksheets();
    }
}
