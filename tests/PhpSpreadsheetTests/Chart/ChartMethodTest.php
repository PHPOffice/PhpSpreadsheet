<?php

namespace PhpOffice\PhpSpreadsheetTests\Chart;

use PhpOffice\PhpSpreadsheet\Chart\Axis;
use PhpOffice\PhpSpreadsheet\Chart\Chart;
use PhpOffice\PhpSpreadsheet\Chart\DataSeries;
use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
use PhpOffice\PhpSpreadsheet\Chart\Legend as ChartLegend;
use PhpOffice\PhpSpreadsheet\Chart\PlotArea;
use PhpOffice\PhpSpreadsheet\Chart\Title;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

class ChartMethodTest extends TestCase
{
    public function testMethodVsConstructor(): void
    {
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

        // Set the Labels for each data series we want to plot
        //     Datatype
        //     Cell reference for data
        //     Format Code
        //     Number of datapoints in series
        //     Data values
        //     Data Marker
        $dataSeriesLabels = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$B$1', null, 1), // 2010
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$C$1', null, 1), // 2011
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$D$1', null, 1), // 2012
        ];
        // Set the X-Axis Labels
        //     Datatype
        //     Cell reference for data
        //     Format Code
        //     Number of datapoints in series
        //     Data values
        //     Data Marker
        $xAxisTickValues = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$A$2:$A$5', null, 4), // Q1 to Q4
        ];
        // Set the Data values for each data series we want to plot
        //     Datatype
        //     Cell reference for data
        //     Format Code
        //     Number of datapoints in series
        //     Data values
        //     Data Marker
        $dataSeriesValues = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$B$2:$B$5', null, 4),
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$C$2:$C$5', null, 4),
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$D$2:$D$5', null, 4),
        ];

        // Build the dataseries
        $series = new DataSeries(
            DataSeries::TYPE_LINECHART, // plotType
            DataSeries::GROUPING_PERCENT_STACKED, // plotGrouping
            range(0, count($dataSeriesValues) - 1), // plotOrder
            $dataSeriesLabels, // plotLabel
            $xAxisTickValues, // plotCategory
            $dataSeriesValues          // plotValues
        );

        // Set the series in the plot area
        $plotArea = new PlotArea(null, [$series]);
        $title = new Title('Method vs Constructor test');
        $legend = new ChartLegend(ChartLegend::POSITION_TOPRIGHT, null, false);
        $xAxis = new Axis();
        $yAxis = new Axis();
        $xAxisLabel = new Title('X-Axis label');
        $yAxisLabel = new Title('Y-Axis label');
        $chart1 = new Chart(
            'chart1', // name
            $title, // title
            $legend, // legend
            $plotArea, // plotArea
            true, // plotVisibleOnly
            DataSeries::EMPTY_AS_GAP, // displayBlanksAs
            $xAxisLabel, // xAxisLabel
            $yAxisLabel, // yAxisLabel
            $xAxis, // xAxis
            $yAxis // yAxis
        );
        $chart2 = new Chart('xyz');
        $chart2
            ->setName('chart1')
            ->setLegend($legend)
            ->setPlotArea($plotArea)
            ->setPlotVisibleOnly(true)
            ->setDisplayBlanksAs(DataSeries::EMPTY_AS_GAP)
            ->setChartAxisX($xAxis)
            ->setChartAxisY($yAxis)
            ->setXAxisLabel($xAxisLabel)
            ->setYAxisLabel($yAxisLabel)
            ->setTitle($title);
        self::assertEquals($chart1, $chart2);
        $spreadsheet->disconnectWorksheets();
    }

    public function testPositions(): void
    {
        $chart = new Chart('chart1');
        $chart->setTopLeftPosition('B3', 2, 4);
        self::assertSame('B3', $chart->getTopLeftCell());
        self::assertEquals(['X' => 2, 'Y' => 4], $chart->getTopLeftOffset());
        self::assertEquals(2, $chart->getTopLeftXOffset());
        self::assertEquals(4, $chart->getTopLeftYOffset());
        $chart->setTopLeftCell('B5');
        self::assertSame('B5', $chart->getTopLeftCell());
        self::assertEquals(2, $chart->getTopLeftXOffset());
        self::assertEquals(4, $chart->getTopLeftYOffset());
        $chart->setTopLeftOffset(6, 8);
        self::assertSame('B5', $chart->getTopLeftCell());
        self::assertEquals(6, $chart->getTopLeftXOffset());
        self::assertEquals(8, $chart->getTopLeftYOffset());

        $chart->setbottomRightPosition('H9', 3, 5);
        self::assertSame('H9', $chart->getBottomRightCell());
        self::assertEquals(['X' => 3, 'Y' => 5], $chart->getBottomRightOffset());
        self::assertEquals(3, $chart->getBottomRightXOffset());
        self::assertEquals(5, $chart->getBottomRightYOffset());
        $chart->setbottomRightCell('H11');
        self::assertSame('H11', $chart->getBottomRightCell());
        self::assertEquals(3, $chart->getBottomRightXOffset());
        self::assertEquals(5, $chart->getBottomRightYOffset());
        $chart->setbottomRightOffset(7, 9);
        self::assertSame('H11', $chart->getBottomRightCell());
        self::assertEquals(7, $chart->getBottomRightXOffset());
        self::assertEquals(9, $chart->getBottomRightYOffset());
    }
}
