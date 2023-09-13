<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Chart;

use PhpOffice\PhpSpreadsheet\Chart\Chart;
use PhpOffice\PhpSpreadsheet\Chart\DataSeries;
use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
use PhpOffice\PhpSpreadsheet\Chart\Legend as ChartLegend;
use PhpOffice\PhpSpreadsheet\Chart\PlotArea;
use PhpOffice\PhpSpreadsheet\Chart\Title;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class DataSeriesValues2Test extends AbstractFunctional
{
    public function readCharts(XlsxReader $reader): void
    {
        $reader->setIncludeCharts(true);
    }

    public function writeCharts(XlsxWriter $writer): void
    {
        $writer->setIncludeCharts(true);
    }

    public function testDataSeriesValues(): void
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
            null, // plotType
            null, // plotGrouping
            range(0, count($dataSeriesValues) - 1), // plotOrder
            $dataSeriesLabels, // plotLabel
            $xAxisTickValues, // plotCategory
            $dataSeriesValues          // plotValues
        );
        self::assertEmpty($series->getPlotType());
        self::assertEmpty($series->getPlotGrouping());
        self::assertFalse($series->getSmoothLine());
        $series->setPlotType(DataSeries::TYPE_AREACHART);
        $series->setPlotGrouping(DataSeries::GROUPING_PERCENT_STACKED);
        $series->setSmoothLine(true);
        self::assertSame(DataSeries::TYPE_AREACHART, $series->getPlotType());
        self::assertSame(DataSeries::GROUPING_PERCENT_STACKED, $series->getPlotGrouping());
        self::assertTrue($series->getSmoothLine());

        // Set the series in the plot area
        $plotArea = new PlotArea(null, [$series]);
        // Set the chart legend
        $legend = new ChartLegend(ChartLegend::POSITION_TOPRIGHT, null, false);

        $title = new Title('Test %age-Stacked Area Chart');
        $yAxisLabel = new Title('Value ($k)');

        // Create the chart
        $chart = new Chart(
            'chart1', // name
            $title, // title
            $legend, // legend
            $plotArea, // plotArea
            true, // plotVisibleOnly
            DataSeries::EMPTY_AS_GAP, // displayBlanksAs
            null, // xAxisLabel
            $yAxisLabel  // yAxisLabel
        );

        // Set the position where the chart should appear in the worksheet
        $chart->setTopLeftPosition('A7');
        $chart->setBottomRightPosition('H20');

        // Add the chart to the worksheet
        $worksheet->addChart($chart);

        $plotArea = $chart->getPlotArea();
        self::assertNotNull($plotArea);
        self::assertSame(1, $plotArea->getPlotGroupCount());
        $plotValues = $plotArea->getPlotGroup()[0]->getPlotValues();
        self::assertCount(3, $plotValues);
        self::assertSame([], $plotValues[1]->getDataValues());
        self::assertNull($plotValues[1]->getDataValue());

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
        $plotArea2 = $chart2->getPlotArea();
        self::assertNotNull($plotArea2);
        $plotGroup2 = $plotArea2->getPlotGroup()[0];
        self::assertNotNull($plotGroup2);
        $plotValues2 = $plotGroup2->getPlotValues();
        self::assertCount(3, $plotValues2);
        self::assertSame([15.0, 73.0, 61.0, 32.0], $plotValues2[1]->getDataValues());
        self::assertSame([15.0, 73.0, 61.0, 32.0], $plotValues2[1]->getDataValue());
        $labels2 = $plotGroup2->getPlotLabels();
        self::assertCount(3, $labels2);
        self::assertEquals(2010, $labels2[0]->getDataValue());
        $dataSeries = $plotArea2->getPlotGroup()[0];
        self::assertFalse($dataSeries->getPlotValuesByIndex(99));
        self::assertNotFalse($dataSeries->getPlotValuesByIndex(0));
        self::assertEquals([12, 56, 52, 30], $dataSeries->getPlotValuesByIndex(0)->getDataValues());
        self::assertSame(DataSeries::TYPE_AREACHART, $dataSeries->getPlotType());
        self::assertSame(DataSeries::GROUPING_PERCENT_STACKED, $dataSeries->getPlotGrouping());
        // SmoothLine written out for DataSeries only for LineChart.
        // Original test was wrong - used $chart rather than $chart2
        //   to retrieve data which was read in.
        //self::assertTrue($dataSeries->getSmoothLine());

        $reloadedSpreadsheet->disconnectWorksheets();
    }

    public function testSomeProperties(): void
    {
        $dataSeriesValues = new DataSeriesValues();
        self::assertNull($dataSeriesValues->getDataSource());
        self::assertEmpty($dataSeriesValues->getPointMarker());
        self::assertSame(3, $dataSeriesValues->getPointSize());
        $dataSeriesValues->setDataSource('Worksheet!$B$1')
            ->setPointMarker('square')
            ->setPointSize(6);
        self::assertSame('Worksheet!$B$1', $dataSeriesValues->getDataSource());
        self::assertSame('square', $dataSeriesValues->getPointMarker());
        self::assertSame(6, $dataSeriesValues->getPointSize());
    }
}
