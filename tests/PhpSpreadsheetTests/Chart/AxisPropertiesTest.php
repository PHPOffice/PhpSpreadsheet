<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Chart;

use PhpOffice\PhpSpreadsheet\Chart\Axis;
use PhpOffice\PhpSpreadsheet\Chart\Chart;
use PhpOffice\PhpSpreadsheet\Chart\DataSeries;
use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
use PhpOffice\PhpSpreadsheet\Chart\Legend as ChartLegend;
use PhpOffice\PhpSpreadsheet\Chart\PlotArea;
use PhpOffice\PhpSpreadsheet\Chart\Properties;
use PhpOffice\PhpSpreadsheet\Chart\Title;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class AxisPropertiesTest extends AbstractFunctional
{
    public function readCharts(XlsxReader $reader): void
    {
        $reader->setIncludeCharts(true);
    }

    public function writeCharts(XlsxWriter $writer): void
    {
        $writer->setIncludeCharts(true);
    }

    public function testAxisProperties(): void
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
        $xAxis = new Axis();
        $xAxis->setFillParameters('FF0000', null, 'srgbClr');
        self::assertSame('FF0000', $xAxis->getFillProperty('value'));
        self::assertSame('', $xAxis->getFillProperty('alpha'));
        self::assertSame('srgbClr', $xAxis->getFillProperty('type'));

        $xAxis->setAxisOptionsProperties(
            Properties::AXIS_LABELS_HIGH, // axisLabels,
            null, // $horizontalCrossesValue,
            Properties::HORIZONTAL_CROSSES_MAXIMUM, //horizontalCrosses
            Properties::ORIENTATION_REVERSED, //axisOrientation
            Properties::TICK_MARK_INSIDE, //majorTmt
            Properties::TICK_MARK_OUTSIDE, //minorTmt
            '8', //minimum
            '68', //maximum
            '20', //majorUnit
            '5', //minorUnit
            '6', //textRotation
            '0', //hidden
        );
        self::assertSame(Properties::AXIS_LABELS_HIGH, $xAxis->getAxisOptionsProperty('axis_labels'));
        self::assertNull($xAxis->getAxisOptionsProperty('horizontal_crosses_value'));
        self::assertSame(Properties::HORIZONTAL_CROSSES_MAXIMUM, $xAxis->getAxisOptionsProperty('horizontal_crosses'));
        self::assertSame(Properties::ORIENTATION_REVERSED, $xAxis->getAxisOptionsProperty('orientation'));
        self::assertSame(Properties::TICK_MARK_INSIDE, $xAxis->getAxisOptionsProperty('major_tick_mark'));
        self::assertSame(Properties::TICK_MARK_OUTSIDE, $xAxis->getAxisOptionsProperty('minor_tick_mark'));
        self::assertSame('8', $xAxis->getAxisOptionsProperty('minimum'));
        self::assertSame('68', $xAxis->getAxisOptionsProperty('maximum'));
        self::assertSame('20', $xAxis->getAxisOptionsProperty('major_unit'));
        self::assertSame('5', $xAxis->getAxisOptionsProperty('minor_unit'));
        self::assertSame('6', $xAxis->getAxisOptionsProperty('textRotation'));
        self::assertSame('0', $xAxis->getAxisOptionsProperty('hidden'));

        $yAxis = new Axis();
        $yAxis->setFillParameters('accent1', 30, 'schemeClr');
        self::assertSame('accent1', $yAxis->getFillProperty('value'));
        self::assertSame('30', $yAxis->getFillProperty('alpha'));
        self::assertSame('schemeClr', $yAxis->getFillProperty('type'));

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
            $xAxis, // xAxis
            $yAxis, // yAxis
            null, //majorGridlines,
            null // minorGridlines
        );
        $xAxis2 = $chart->getChartAxisX();
        self::assertSame('FF0000', $xAxis2->getFillProperty('value'));
        self::assertSame('', $xAxis2->getFillProperty('alpha'));
        self::assertSame('srgbClr', $xAxis2->getFillProperty('type'));

        self::assertSame(Properties::AXIS_LABELS_HIGH, $xAxis2->getAxisOptionsProperty('axis_labels'));
        self::assertNull($xAxis2->getAxisOptionsProperty('horizontal_crosses_value'));
        self::assertSame(Properties::HORIZONTAL_CROSSES_MAXIMUM, $xAxis2->getAxisOptionsProperty('horizontal_crosses'));
        self::assertSame(Properties::ORIENTATION_REVERSED, $xAxis2->getAxisOptionsProperty('orientation'));
        self::assertSame(Properties::TICK_MARK_INSIDE, $xAxis2->getAxisOptionsProperty('major_tick_mark'));
        self::assertSame(Properties::TICK_MARK_OUTSIDE, $xAxis2->getAxisOptionsProperty('minor_tick_mark'));
        self::assertSame('8', $xAxis2->getAxisOptionsProperty('minimum'));
        self::assertSame('68', $xAxis2->getAxisOptionsProperty('maximum'));
        self::assertSame('20', $xAxis2->getAxisOptionsProperty('major_unit'));
        self::assertSame('5', $xAxis2->getAxisOptionsProperty('minor_unit'));
        self::assertSame('6', $xAxis2->getAxisOptionsProperty('textRotation'));
        self::assertSame('0', $xAxis2->getAxisOptionsProperty('hidden'));

        $yAxis2 = $chart->getChartAxisY();
        self::assertSame('accent1', $yAxis2->getFillProperty('value'));
        self::assertSame('30', $yAxis2->getFillProperty('alpha'));
        self::assertSame('schemeClr', $yAxis2->getFillProperty('type'));

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
        $xAxis3 = $chart2->getChartAxisX();
        self::assertSame('FF0000', $xAxis3->getFillProperty('value'));
        self::assertSame('', $xAxis3->getFillProperty('alpha'));
        self::assertSame('srgbClr', $xAxis3->getFillProperty('type'));

        self::assertSame(Properties::AXIS_LABELS_HIGH, $xAxis3->getAxisOptionsProperty('axis_labels'));
        self::assertSame(Properties::TICK_MARK_INSIDE, $xAxis3->getAxisOptionsProperty('major_tick_mark'));
        self::assertSame(Properties::TICK_MARK_OUTSIDE, $xAxis3->getAxisOptionsProperty('minor_tick_mark'));
        self::assertNull($xAxis3->getAxisOptionsProperty('horizontal_crosses_value'));
        self::assertSame(Properties::HORIZONTAL_CROSSES_MAXIMUM, $xAxis3->getAxisOptionsProperty('horizontal_crosses'));
        self::assertSame(Properties::ORIENTATION_REVERSED, $xAxis3->getAxisOptionsProperty('orientation'));
        self::assertSame('8', $xAxis3->getAxisOptionsProperty('minimum'));
        self::assertSame('68', $xAxis3->getAxisOptionsProperty('maximum'));
        self::assertSame('20', $xAxis3->getAxisOptionsProperty('major_unit'));
        self::assertSame('5', $xAxis3->getAxisOptionsProperty('minor_unit'));
        self::assertSame('6', $xAxis3->getAxisOptionsProperty('textRotation'));
        self::assertSame('0', $xAxis3->getAxisOptionsProperty('hidden'));

        $yAxis3 = $chart2->getChartAxisY();
        self::assertSame('accent1', $yAxis3->getFillProperty('value'));
        self::assertSame('30', $yAxis3->getFillProperty('alpha'));
        self::assertSame('schemeClr', $yAxis3->getFillProperty('type'));

        $xAxis3->setAxisOrientation(Properties::ORIENTATION_NORMAL);
        self::assertSame(Properties::ORIENTATION_NORMAL, $xAxis3->getAxisOptionsProperty('orientation'));
        $xAxis3->setAxisOptionsProperties(
            Properties::AXIS_LABELS_HIGH, // axisLabels,
            '5' // $horizontalCrossesValue,
        );
        self::assertSame('5', $xAxis3->getAxisOptionsProperty('horizontal_crosses_value'));

        $yAxis3->setLineColorProperties('0000FF', null, 'srgbClr');
        self::assertSame('0000FF', $yAxis3->getLineColorProperty('value'));
        self::assertNull($yAxis3->getLineColorProperty('alpha'));
        self::assertSame('srgbClr', $yAxis3->getLineColorProperty('type'));
        $yAxis3->setAxisNumberProperties(Properties::FORMAT_CODE_GENERAL);
        self::assertFalse($yAxis3->getAxisIsNumericFormat());
        $yAxis3->setAxisNumberProperties(Properties::FORMAT_CODE_NUMBER);
        self::assertTrue($yAxis3->getAxisIsNumericFormat());

        $reloadedSpreadsheet->disconnectWorksheets();
    }
}
