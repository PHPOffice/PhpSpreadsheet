<?php

namespace PhpOffice\PhpSpreadsheetTests\Chart;

use PhpOffice\PhpSpreadsheet\Chart\Axis;
use PhpOffice\PhpSpreadsheet\Chart\Chart;
use PhpOffice\PhpSpreadsheet\Chart\ChartColor;
use PhpOffice\PhpSpreadsheet\Chart\DataSeries;
use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
use PhpOffice\PhpSpreadsheet\Chart\GridLines;
use PhpOffice\PhpSpreadsheet\Chart\Legend as ChartLegend;
use PhpOffice\PhpSpreadsheet\Chart\PlotArea;
use PhpOffice\PhpSpreadsheet\Chart\Title;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class GridlinesShadowGlowTest extends AbstractFunctional
{
    public function readCharts(XlsxReader $reader): void
    {
        $reader->setIncludeCharts(true);
    }

    public function writeCharts(XlsxWriter $writer): void
    {
        $writer->setIncludeCharts(true);
    }

    public function testGlowY(): void
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
        // Set the chart legend
        $legend = new ChartLegend(ChartLegend::POSITION_TOPRIGHT, null, false);

        $title = new Title('Test %age-Stacked Area Chart');
        $yAxisLabel = new Title('Value ($k)');
        $yAxis = new Axis();
        $majorGridlines = new GridLines();
        $yAxis->setMajorGridlines($majorGridlines);
        $majorGlowSize = 10.0;
        $majorGridlines->setGlowProperties($majorGlowSize, 'FFFF00', 30, ChartColor::EXCEL_COLOR_TYPE_RGB);
        $softEdgeSize = 2.5;
        $majorGridlines->setSoftEdges($softEdgeSize);
        $expectedGlowColor = [
            'type' => 'srgbClr',
            'value' => 'FFFF00',
            'alpha' => 30,
        ];
        self::assertEquals($majorGlowSize, $majorGridlines->getGlowProperty('size'));
        self::assertEquals($majorGlowSize, $majorGridlines->getGlowSize());
        self::assertEquals($expectedGlowColor['value'], $majorGridlines->getGlowColor('value'));
        self::assertEquals($expectedGlowColor, $majorGridlines->getGlowProperty('color'));
        self::assertEquals($softEdgeSize, $majorGridlines->getSoftEdgesSize());

        $minorGridlines = new GridLines();
        $yAxis->setMinorGridlines($minorGridlines);
        $expectedShadow = [
            'effect' => 'outerShdw',
            'algn' => 'tl',
            'blur' => 4,
            'direction' => 45,
            'distance' => 3,
            'rotWithShape' => 0,
            'color' => [
                'type' => ChartColor::EXCEL_COLOR_TYPE_STANDARD,
                'value' => 'black',
                'alpha' => 40,
            ],
        ];
        foreach ($expectedShadow as $key => $value) {
            $minorGridlines->setShadowProperty($key, $value);
        }
        foreach ($expectedShadow as $key => $value) {
            self::assertEquals($value, $minorGridlines->getShadowProperty($key), $key);
        }
        $testShadow2 = $minorGridlines->getShadowArray();
        self::assertNull($testShadow2['presets']);
        self::assertEquals(['sx' => null, 'sy' => null, 'kx' => null, 'ky' => null], $testShadow2['size']);
        unset($testShadow2['presets'], $testShadow2['size']);
        self::assertEquals($expectedShadow, $testShadow2);

        // Create the chart
        $chart = new Chart(
            'chart1', // name
            $title, // title
            $legend, // legend
            $plotArea, // plotArea
            true, // plotVisibleOnly
            DataSeries::EMPTY_AS_GAP, // displayBlanksAs
            null, // xAxisLabel
            $yAxisLabel,  // yAxisLabel
            null, // xAxis
            $yAxis // yAxis
        );
        $yAxis2 = $chart->getChartAxisY();
        $majorGridlines2 = $yAxis2->getMajorGridlines();
        self::assertNotNull($majorGridlines2);
        self::assertEquals($majorGlowSize, $majorGridlines2->getGlowProperty('size'));
        self::assertEquals($expectedGlowColor, $majorGridlines2->getGlowProperty('color'));
        self::assertEquals($softEdgeSize, $majorGridlines2->getSoftEdgesSize());
        $minorGridlines2 = $yAxis2->getMinorGridlines();
        self::assertNotNull($minorGridlines2);
        foreach ($expectedShadow as $key => $value) {
            self::assertEquals($value, $minorGridlines2->getShadowProperty($key), $key);
        }

        // Set the position where the chart should appear in the worksheet
        $chart->setTopLeftPosition('A7');
        $chart->setBottomRightPosition('H20');

        // Add the chart to the worksheet
        $worksheet->addChart($chart);

        /** @var callable */
        $callableReader = [$this, 'readCharts'];
        /** @var callable */
        $callableWriter = [$this, 'writeCharts'];
        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, 'Xlsx', $callableReader, $callableWriter);
        $spreadsheet->disconnectWorksheets();

        $sheet = $reloadedSpreadsheet->getActiveSheet();
        $charts2 = $sheet->getChartCollection();
        self::assertCount(1, $charts2);
        $chart2 = $charts2[0];
        self::assertNotNull($chart2);
        $yAxis3 = $chart2->getChartAxisY();
        $majorGridlines3 = $yAxis3->getMajorGridlines();
        self::assertNotNull($majorGridlines3);
        self::assertEquals($majorGlowSize, $majorGridlines3->getGlowProperty('size'));
        self::assertEquals($expectedGlowColor, $majorGridlines3->getGlowProperty('color'));
        self::assertEquals($softEdgeSize, $majorGridlines3->getSoftEdgesSize());
        $minorGridlines3 = $yAxis3->getMinorGridlines();
        self::assertNotNull($minorGridlines3);
        foreach ($expectedShadow as $key => $value) {
            self::assertEquals($value, $minorGridlines3->getShadowProperty($key), $key);
        }

        $reloadedSpreadsheet->disconnectWorksheets();
    }
}
