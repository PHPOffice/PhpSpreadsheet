<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Chart;

use PhpOffice\PhpSpreadsheet\Chart\Chart;
use PhpOffice\PhpSpreadsheet\Chart\DataSeries;
use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
use PhpOffice\PhpSpreadsheet\Chart\Layout;
use PhpOffice\PhpSpreadsheet\Chart\Legend as ChartLegend;
use PhpOffice\PhpSpreadsheet\Chart\PlotArea;
use PhpOffice\PhpSpreadsheet\Chart\Properties;
use PhpOffice\PhpSpreadsheet\Chart\Title;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class ChartCloneTest extends AbstractFunctional
{
    private const DIRECTORY = 'samples' . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR;

    public function testCloneSheet(): void
    {
        $file = self::DIRECTORY . '32readwriteLineChart5.xlsx';
        $reader = new XlsxReader();
        $reader->setIncludeCharts(true);
        $spreadsheet = $reader->load($file);
        $oldSheet = $spreadsheet->getActiveSheet();

        $sheet = clone $oldSheet;
        $sheet->setTitle('test2');
        $spreadsheet->addsheet($sheet);

        $oldCharts = $oldSheet->getChartCollection();
        self::assertCount(1, $oldCharts);
        $oldChart = $oldCharts[0];
        self::assertNotNull($oldChart);

        $charts = $sheet->getChartCollection();
        self::assertCount(1, $charts);
        $chart = $charts[0];
        self::assertNotNull($chart);
        self::assertNotSame($oldChart, $chart);

        self::assertSame('ffffff', $chart->getFillColor()->getValue());
        self::assertSame('srgbClr', $chart->getFillColor()->getType());
        self::assertSame('d9d9d9', $chart->getBorderLines()->getLineColorProperty('value'));
        self::assertSame('srgbClr', $chart->getBorderLines()->getLineColorProperty('type'));
        self::assertEqualsWithDelta(9360 / Properties::POINTS_WIDTH_MULTIPLIER, $chart->getBorderLines()->getLineStyleProperty('width'), 1.0E-8);
        self::assertTrue($chart->getChartAxisY()->getNoFill());
        self::assertFalse($chart->getChartAxisX()->getNoFill());

        $spreadsheet->disconnectWorksheets();
    }

    public function testCloneSheetWithLegendAndTitle(): void
    {
        $file = self::DIRECTORY . '32readwriteChartWithImages1.xlsx';
        $reader = new XlsxReader();
        $reader->setIncludeCharts(true);
        $spreadsheet = $reader->load($file);
        $oldSheet = $spreadsheet->getActiveSheet();

        $sheet = clone $oldSheet;
        $sheet->setTitle('test2');
        $spreadsheet->addsheet($sheet);

        $oldCharts = $oldSheet->getChartCollection();
        self::assertCount(1, $oldCharts);
        $oldChart = $oldCharts[0];
        self::assertNotNull($oldChart);

        $charts = $sheet->getChartCollection();
        self::assertCount(1, $charts);
        $chart = $charts[0];
        self::assertNotNull($chart);
        self::assertNotSame($oldChart, $chart);

        self::assertNotNull($chart->getLegend());
        self::assertNotSame($chart->getLegend(), $oldChart->getLegend());
        self::assertNotNull($chart->getTitle());
        self::assertNotSame($chart->getTitle(), $oldChart->getTitle());

        $spreadsheet->disconnectWorksheets();
    }

    public function testCloneSheetWithBubbleSizes(): void
    {
        $file = self::DIRECTORY . '32readwriteBubbleChart2.xlsx';
        $reader = new XlsxReader();
        $reader->setIncludeCharts(true);
        $spreadsheet = $reader->load($file);
        $oldSheet = $spreadsheet->getActiveSheet();

        $sheet = clone $oldSheet;
        $sheet->setTitle('test2');
        $spreadsheet->addsheet($sheet);

        $oldCharts = $oldSheet->getChartCollection();
        self::assertCount(1, $oldCharts);
        $oldChart = $oldCharts[0];
        self::assertNotNull($oldChart);

        $charts = $sheet->getChartCollection();
        self::assertCount(1, $charts);
        $chart = $charts[0];
        self::assertNotNull($chart);
        self::assertNotSame($oldChart, $chart);

        $oldGroup = $oldChart->getPlotArea()?->getPlotGroup();
        self::assertNotNull($oldGroup);
        self::assertCount(1, $oldGroup);
        $oldSizes = $oldGroup[0]->getPlotBubbleSizes();
        self::assertCount(2, $oldSizes);

        $plotGroup = $chart->getPlotArea()?->getPlotGroup();
        self::assertNotNull($plotGroup);
        self::assertCount(1, $plotGroup);
        $bubbleSizes = $plotGroup[0]->getPlotBubbleSizes();
        self::assertCount(2, $bubbleSizes);
        self::assertNotSame($bubbleSizes, $oldSizes);

        $spreadsheet->disconnectWorksheets();
    }

    public function testCloneSheetWithTrendLines(): void
    {
        $file = self::DIRECTORY . '32readwriteScatterChartTrendlines1.xlsx';
        $reader = new XlsxReader();
        $reader->setIncludeCharts(true);
        $spreadsheet = $reader->load($file);
        $oldSheet = $spreadsheet->getActiveSheet();

        $sheet = clone $oldSheet;
        $sheet->setTitle('test2');
        $spreadsheet->addsheet($sheet);

        $oldCharts = $oldSheet->getChartCollection();
        self::assertCount(2, $oldCharts);
        $oldChart = $oldCharts[1];
        self::assertNotNull($oldChart);

        $charts = $sheet->getChartCollection();
        self::assertCount(2, $charts);
        $chart = $charts[1];
        self::assertNotNull($chart);
        self::assertNotSame($oldChart, $chart);

        $oldGroup = $chart->getPlotArea()?->getPlotGroup();
        self::assertNotNull($oldGroup);
        self::assertCount(1, $oldGroup);
        $oldLabels = $oldGroup[0]->getPlotLabels();
        self::assertCount(1, $oldLabels);
        self::assertCount(3, $oldLabels[0]->getTrendLines());

        $plotGroup = $chart->getPlotArea()?->getPlotGroup();
        self::assertNotNull($plotGroup);
        self::assertCount(1, $plotGroup);
        $plotLabels = $plotGroup[0]->getPlotLabels();
        self::assertCount(1, $plotLabels);
        self::assertCount(3, $plotLabels[0]->getTrendLines());

        $spreadsheet->disconnectWorksheets();
    }

    public function testCloneFillColorArray(): void
    {
        // code borrowed from BarChartCustomColorsTest
        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $worksheet->fromArray(
            [
                ['', 2010, 2011, 2012],
                ['Q1', 12, 15, 21],
                ['Q2', 56, 73, 86],
                ['Q3', 52, 61, 69],
                ['Q4', 30, 32, 0],
            ]
        );
        // Custom colors for dataSeries (gray, blue, red, orange)
        $colors = [
            'cccccc',
            '*accent1', // use schemeClr, was '00abb8',
            '/green', // use prstClr, was 'b8292f',
            'eb8500',
        ];

        // Set the Labels for each data series we want to plot
        //     Datatype
        //     Cell reference for data
        //     Format Code
        //     Number of datapoints in series
        //     Data values
        //     Data Marker
        $dataSeriesLabels1 = [
            new DataSeriesValues(
                DataSeriesValues::DATASERIES_TYPE_STRING,
                'Worksheet!$C$1',
                null,
                1
            ), // 2011
        ];
        // Set the X-Axis Labels
        //     Datatype
        //     Cell reference for data
        //     Format Code
        //     Number of datapoints in series
        //     Data values
        //     Data Marker
        $xAxisTickValues1 = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$A$2:$A$5', null, 4), // Q1 to Q4
        ];
        // Set the Data values for each data series we want to plot
        //     Datatype
        //     Cell reference for data
        //     Format Code
        //     Number of datapoints in series
        //     Data values
        //     Data Marker
        //     Custom Colors
        $dataSeriesValues1Element = new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$C$2:$C$5', null, 4);
        $dataSeriesValues1Element->setFillColor($colors);
        $dataSeriesValues1 = [$dataSeriesValues1Element];

        // Build the dataseries
        $series1 = new DataSeries(
            DataSeries::TYPE_PIECHART, // plotType
            null, // plotGrouping (Pie charts don't have any grouping)
            range(0, count($dataSeriesValues1) - 1), // plotOrder
            $dataSeriesLabels1, // plotLabel
            $xAxisTickValues1, // plotCategory
            $dataSeriesValues1          // plotValues
        );

        // Set up a layout object for the Pie chart
        $layout1 = new Layout();
        $layout1->setShowVal(true);
        $layout1->setShowPercent(true);

        // Set the series in the plot area
        $plotArea1 = new PlotArea($layout1, [$series1]);
        // Set the chart legend
        $legend1 = new ChartLegend(ChartLegend::POSITION_RIGHT, null, false);

        $title1 = new Title('Test Pie Chart');

        // Create the chart
        $chart1 = new Chart(
            'chart1', // name
            $title1, // title
            $legend1, // legend
            $plotArea1, // plotArea
            true, // plotVisibleOnly
            DataSeries::EMPTY_AS_GAP, // displayBlanksAs
            null, // xAxisLabel
            null  // no Y-Axis for Pie Chart
        );

        // Set the position where the chart should appear in the worksheet
        $chart1->setTopLeftPosition('A7');
        $chart1->setBottomRightPosition('H20');

        // Add the chart to the worksheet
        $worksheet->addChart($chart1);

        $sheet = clone $worksheet;
        $sheet->setTitle('test2');
        $spreadsheet->addsheet($sheet);

        $charts2 = $sheet->getChartCollection();
        self::assertCount(1, $charts2);
        $chart2 = $charts2[0];
        self::assertNotNull($chart2);
        self::assertSame('Test Pie Chart', $chart2->getTitle()?->getCaption());
        $plotArea2 = $chart2->getPlotArea();
        self::assertNotNull($plotArea2);
        $dataSeries2 = $plotArea2->getPlotGroup();
        self::assertCount(1, $dataSeries2);
        $plotValues = $dataSeries2[0]->getPlotValues();
        self::assertCount(1, $plotValues);
        $fillColors = $plotValues[0]->getFillColor();
        self::assertSame($colors, $fillColors);

        $spreadsheet->disconnectWorksheets();
    }
}
