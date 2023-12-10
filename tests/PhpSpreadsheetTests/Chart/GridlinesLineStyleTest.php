<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Chart;

use PhpOffice\PhpSpreadsheet\Chart\Axis;
use PhpOffice\PhpSpreadsheet\Chart\Chart;
use PhpOffice\PhpSpreadsheet\Chart\DataSeries;
use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
use PhpOffice\PhpSpreadsheet\Chart\GridLines;
use PhpOffice\PhpSpreadsheet\Chart\Legend as ChartLegend;
use PhpOffice\PhpSpreadsheet\Chart\PlotArea;
use PhpOffice\PhpSpreadsheet\Chart\Properties;
use PhpOffice\PhpSpreadsheet\Chart\Title;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class GridlinesLineStyleTest extends AbstractFunctional
{
    public function readCharts(XlsxReader $reader): void
    {
        $reader->setIncludeCharts(true);
    }

    public function writeCharts(XlsxWriter $writer): void
    {
        $writer->setIncludeCharts(true);
    }

    public function testLineStyles(): void
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
        $majorGridlines = new GridLines();
        $width = 2;
        $compound = Properties::LINE_STYLE_COMPOUND_THICKTHIN;
        $dash = Properties::LINE_STYLE_DASH_ROUND_DOT;
        $cap = Properties::LINE_STYLE_CAP_ROUND;
        $join = Properties::LINE_STYLE_JOIN_MITER;
        $headArrowType = Properties::LINE_STYLE_ARROW_TYPE_DIAMOND;
        $headArrowSize = Properties::LINE_STYLE_ARROW_SIZE_2;
        $endArrowType = Properties::LINE_STYLE_ARROW_TYPE_OVAL;
        $endArrowSize = Properties::LINE_STYLE_ARROW_SIZE_3;
        $majorGridlines->setLineStyleProperties(
            $width,
            $compound,
            $dash,
            $cap,
            $join,
            $headArrowType,
            $headArrowSize,
            $endArrowType,
            $endArrowSize
        );
        $minorGridlines = new GridLines();
        $minorGridlines->setLineColorProperties('00FF00', 30, 'srgbClr');

        self::assertEquals($width, $majorGridlines->getLineStyleProperty('width'));
        self::assertEquals($compound, $majorGridlines->getLineStyleProperty('compound'));
        self::assertEquals($dash, $majorGridlines->getLineStyleProperty('dash'));
        self::assertEquals($cap, $majorGridlines->getLineStyleProperty('cap'));
        self::assertEquals($join, $majorGridlines->getLineStyleProperty('join'));
        self::assertEquals($headArrowType, $majorGridlines->getLineStyleProperty(['arrow', 'head', 'type']));
        self::assertEquals($headArrowSize, $majorGridlines->getLineStyleProperty(['arrow', 'head', 'size']));
        self::assertEquals($endArrowType, $majorGridlines->getLineStyleProperty(['arrow', 'end', 'type']));
        self::assertEquals($endArrowSize, $majorGridlines->getLineStyleProperty(['arrow', 'end', 'size']));
        self::assertEquals('sm', $majorGridlines->getLineStyleProperty(['arrow', 'head', 'w']));
        self::assertEquals('med', $majorGridlines->getLineStyleProperty(['arrow', 'head', 'len']));
        self::assertEquals('sm', $majorGridlines->getLineStyleProperty(['arrow', 'end', 'w']));
        self::assertEquals('lg', $majorGridlines->getLineStyleProperty(['arrow', 'end', 'len']));
        self::assertEquals('sm', $majorGridlines->getLineStyleArrowWidth('end'));
        self::assertEquals('lg', $majorGridlines->getLineStyleArrowLength('end'));
        self::assertEquals('lg', $majorGridlines->getLineStyleArrowParameters('end', 'len'));

        self::assertSame('00FF00', $minorGridlines->getLineColorProperty('value'));
        self::assertSame(30, $minorGridlines->getLineColorProperty('alpha'));
        self::assertSame('srgbClr', $minorGridlines->getLineColorProperty('type'));

        // Create the chart
        $yAxis = new Axis();
        $yAxis->setMajorGridlines($majorGridlines);
        $yAxis->setMinorGridlines($minorGridlines);
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
        self::assertEquals($width, $majorGridlines2->getLineStyleProperty('width'));
        self::assertEquals($compound, $majorGridlines2->getLineStyleProperty('compound'));
        self::assertEquals($dash, $majorGridlines2->getLineStyleProperty('dash'));
        self::assertEquals($cap, $majorGridlines2->getLineStyleProperty('cap'));
        self::assertEquals($join, $majorGridlines2->getLineStyleProperty('join'));
        self::assertEquals($headArrowType, $majorGridlines2->getLineStyleProperty(['arrow', 'head', 'type']));
        self::assertEquals($headArrowSize, $majorGridlines2->getLineStyleProperty(['arrow', 'head', 'size']));
        self::assertEquals($endArrowType, $majorGridlines2->getLineStyleProperty(['arrow', 'end', 'type']));
        self::assertEquals($endArrowSize, $majorGridlines2->getLineStyleProperty(['arrow', 'end', 'size']));
        self::assertEquals('sm', $majorGridlines2->getLineStyleProperty(['arrow', 'head', 'w']));
        self::assertEquals('med', $majorGridlines2->getLineStyleProperty(['arrow', 'head', 'len']));
        self::assertEquals('sm', $majorGridlines2->getLineStyleProperty(['arrow', 'end', 'w']));
        self::assertEquals('lg', $majorGridlines2->getLineStyleProperty(['arrow', 'end', 'len']));

        $minorGridlines2 = $yAxis2->getMinorGridlines();
        self::assertNotNull($minorGridlines2);
        self::assertSame('00FF00', $minorGridlines2->getLineColorProperty('value'));
        self::assertSame(30, $minorGridlines2->getLineColorProperty('alpha'));
        self::assertSame('srgbClr', $minorGridlines2->getLineColorProperty('type'));

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
        self::assertSame('A7', $chart2->getTopLeftCell());
        self::assertSame('H20', $chart2->getBottomRightCell());
        self::assertSame($sheet, $chart2->getWorksheet());
        $yAxis3 = $chart2->getChartAxisY();
        $majorGridlines3 = $yAxis3->getMajorGridlines();
        self::assertNotNull($majorGridlines3);
        self::assertEquals($width, $majorGridlines3->getLineStyleProperty('width'));
        self::assertEquals($compound, $majorGridlines3->getLineStyleProperty('compound'));
        self::assertEquals($dash, $majorGridlines3->getLineStyleProperty('dash'));
        self::assertEquals($cap, $majorGridlines3->getLineStyleProperty('cap'));
        self::assertEquals($join, $majorGridlines3->getLineStyleProperty('join'));
        self::assertEquals($headArrowType, $majorGridlines3->getLineStyleProperty(['arrow', 'head', 'type']));
        self::assertEquals($endArrowType, $majorGridlines3->getLineStyleProperty(['arrow', 'end', 'type']));
        self::assertEquals('sm', $majorGridlines3->getLineStyleProperty(['arrow', 'head', 'w']));
        self::assertEquals('med', $majorGridlines3->getLineStyleProperty(['arrow', 'head', 'len']));
        self::assertEquals('sm', $majorGridlines3->getLineStyleProperty(['arrow', 'end', 'w']));
        self::assertEquals('lg', $majorGridlines3->getLineStyleProperty(['arrow', 'end', 'len']));

        $minorGridlines3 = $yAxis3->getMinorGridlines();
        self::assertNotNull($minorGridlines3);
        self::assertSame('00FF00', $minorGridlines3->getLineColorProperty('value'));
        self::assertSame(30, $minorGridlines3->getLineColorProperty('alpha'));
        self::assertSame('srgbClr', $minorGridlines3->getLineColorProperty('type'));

        $reloadedSpreadsheet->disconnectWorksheets();
    }

    public function testLineStylesDeprecated(): void
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
        $majorGridlines = new GridLines();
        $width = 2;
        $compound = Properties::LINE_STYLE_COMPOUND_THICKTHIN;
        $dash = Properties::LINE_STYLE_DASH_ROUND_DOT;
        $cap = Properties::LINE_STYLE_CAP_ROUND;
        $join = Properties::LINE_STYLE_JOIN_MITER;
        $headArrowType = Properties::LINE_STYLE_ARROW_TYPE_DIAMOND;
        $headArrowSize = Properties::LINE_STYLE_ARROW_SIZE_2;
        $endArrowType = Properties::LINE_STYLE_ARROW_TYPE_OVAL;
        $endArrowSize = Properties::LINE_STYLE_ARROW_SIZE_3;
        $majorGridlines->setLineStyleProperties(
            $width,
            $compound,
            $dash,
            $cap,
            $join,
            $headArrowType,
            $headArrowSize,
            $endArrowType,
            $endArrowSize
        );
        $minorGridlines = new GridLines();
        $minorGridlines->setLineColorProperties('00FF00', 30, 'srgbClr');

        self::assertEquals($width, $majorGridlines->getLineStyleProperty('width'));
        self::assertEquals($compound, $majorGridlines->getLineStyleProperty('compound'));
        self::assertEquals($dash, $majorGridlines->getLineStyleProperty('dash'));
        self::assertEquals($cap, $majorGridlines->getLineStyleProperty('cap'));
        self::assertEquals($join, $majorGridlines->getLineStyleProperty('join'));
        self::assertEquals($headArrowType, $majorGridlines->getLineStyleProperty(['arrow', 'head', 'type']));
        self::assertEquals($headArrowSize, $majorGridlines->getLineStyleProperty(['arrow', 'head', 'size']));
        self::assertEquals($endArrowType, $majorGridlines->getLineStyleProperty(['arrow', 'end', 'type']));
        self::assertEquals($endArrowSize, $majorGridlines->getLineStyleProperty(['arrow', 'end', 'size']));
        self::assertEquals('sm', $majorGridlines->getLineStyleProperty(['arrow', 'head', 'w']));
        self::assertEquals('med', $majorGridlines->getLineStyleProperty(['arrow', 'head', 'len']));
        self::assertEquals('sm', $majorGridlines->getLineStyleProperty(['arrow', 'end', 'w']));
        self::assertEquals('lg', $majorGridlines->getLineStyleProperty(['arrow', 'end', 'len']));
        self::assertEquals('sm', $majorGridlines->getLineStyleArrowWidth('end'));
        self::assertEquals('lg', $majorGridlines->getLineStyleArrowLength('end'));
        self::assertEquals('lg', $majorGridlines->getLineStyleArrowParameters('end', 'len'));

        self::assertSame('00FF00', $minorGridlines->getLineColorProperty('value'));
        self::assertSame(30, $minorGridlines->getLineColorProperty('alpha'));
        self::assertSame('srgbClr', $minorGridlines->getLineColorProperty('type'));

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
            null, // yAxis
            $majorGridlines,
            $minorGridlines // minorGridlines
        );
        $majorGridlines2 = $chart->getChartAxisY()->getMajorGridlines();
        self::assertNotNull($majorGridlines2);
        self::assertEquals($width, $majorGridlines2->getLineStyleProperty('width'));
        self::assertEquals($compound, $majorGridlines2->getLineStyleProperty('compound'));
        self::assertEquals($dash, $majorGridlines2->getLineStyleProperty('dash'));
        self::assertEquals($cap, $majorGridlines2->getLineStyleProperty('cap'));
        self::assertEquals($join, $majorGridlines2->getLineStyleProperty('join'));
        self::assertEquals($headArrowType, $majorGridlines2->getLineStyleProperty(['arrow', 'head', 'type']));
        self::assertEquals($headArrowSize, $majorGridlines2->getLineStyleProperty(['arrow', 'head', 'size']));
        self::assertEquals($endArrowType, $majorGridlines2->getLineStyleProperty(['arrow', 'end', 'type']));
        self::assertEquals($endArrowSize, $majorGridlines2->getLineStyleProperty(['arrow', 'end', 'size']));
        self::assertEquals('sm', $majorGridlines2->getLineStyleProperty(['arrow', 'head', 'w']));
        self::assertEquals('med', $majorGridlines2->getLineStyleProperty(['arrow', 'head', 'len']));
        self::assertEquals('sm', $majorGridlines2->getLineStyleProperty(['arrow', 'end', 'w']));
        self::assertEquals('lg', $majorGridlines2->getLineStyleProperty(['arrow', 'end', 'len']));

        $minorGridlines2 = $chart->getChartAxisY()->getMinorGridlines();
        self::assertNotNull($minorGridlines2);
        self::assertSame('00FF00', $minorGridlines2->getLineColorProperty('value'));
        self::assertSame(30, $minorGridlines2->getLineColorProperty('alpha'));
        self::assertSame('srgbClr', $minorGridlines2->getLineColorProperty('type'));

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
        $majorGridlines3 = $chart2->getChartAxisY()->getMajorGridlines();
        self::assertNotNull($majorGridlines3);
        self::assertEquals($width, $majorGridlines3->getLineStyleProperty('width'));
        self::assertEquals($compound, $majorGridlines3->getLineStyleProperty('compound'));
        self::assertEquals($dash, $majorGridlines3->getLineStyleProperty('dash'));
        self::assertEquals($cap, $majorGridlines3->getLineStyleProperty('cap'));
        self::assertEquals($join, $majorGridlines3->getLineStyleProperty('join'));
        self::assertEquals($headArrowType, $majorGridlines3->getLineStyleProperty(['arrow', 'head', 'type']));
        self::assertEquals($endArrowType, $majorGridlines3->getLineStyleProperty(['arrow', 'end', 'type']));
        self::assertEquals('sm', $majorGridlines3->getLineStyleProperty(['arrow', 'head', 'w']));
        self::assertEquals('med', $majorGridlines3->getLineStyleProperty(['arrow', 'head', 'len']));
        self::assertEquals('sm', $majorGridlines3->getLineStyleProperty(['arrow', 'end', 'w']));
        self::assertEquals('lg', $majorGridlines3->getLineStyleProperty(['arrow', 'end', 'len']));

        $minorGridlines3 = $chart2->getChartAxisY()->getMinorGridlines();
        self::assertNotNull($minorGridlines3);
        self::assertSame('00FF00', $minorGridlines3->getLineColorProperty('value'));
        self::assertSame(30, $minorGridlines3->getLineColorProperty('alpha'));
        self::assertSame('srgbClr', $minorGridlines3->getLineColorProperty('type'));

        $reloadedSpreadsheet->disconnectWorksheets();
    }
}
