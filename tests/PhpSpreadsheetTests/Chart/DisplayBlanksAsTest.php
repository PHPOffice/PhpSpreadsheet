<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Chart;

use PhpOffice\PhpSpreadsheet\Chart\Chart;
use PhpOffice\PhpSpreadsheet\Chart\DataSeries;
use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
use PhpOffice\PhpSpreadsheet\Chart\Legend as ChartLegend;
use PhpOffice\PhpSpreadsheet\Chart\PlotArea;
use PhpOffice\PhpSpreadsheet\Chart\Title;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

class DisplayBlanksAsTest extends TestCase
{
    public function testDisplayBlanksAs(): void
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

        $dataSeriesLabels = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$B$1', null, 1), // 2010
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$C$1', null, 1), // 2011
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$D$1', null, 1), // 2012
        ];

        $xAxisTickValues = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$A$2:$A$5', null, 4), // Q1 to Q4
        ];

        $dataSeriesValues = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$B$2:$B$5', null, 4),
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$C$2:$C$5', null, 4),
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$D$2:$D$5', null, 4),
        ];

        // Build the dataseries
        $series = new DataSeries(
            DataSeries::TYPE_AREACHART, // plotType
            DataSeries::GROUPING_PERCENT_STACKED, // plotGrouping
            range(0, count($dataSeriesValues) - 1), // plotOrder
            $dataSeriesLabels, // plotLabel
            $xAxisTickValues, // plotCategory
            $dataSeriesValues          // plotValues
        );

        $plotArea = new PlotArea(null, [$series]);
        $legend = new ChartLegend(ChartLegend::POSITION_TOPRIGHT, null, false);

        $title = new Title('Test %age-Stacked Area Chart');
        $yAxisLabel = new Title('Value ($k)');

        $chart1 = new Chart(
            'chart1', // name
            $title, // title
            $legend, // legend
            $plotArea, // plotArea
            true, // plotVisibleOnly
            DataSeries::EMPTY_AS_GAP, // displayBlanksAs
            null, // xAxisLabel
            $yAxisLabel  // yAxisLabel
        );
        self::assertSame(DataSeries::EMPTY_AS_GAP, $chart1->getDisplayBlanksAs());
        $chart1->setDisplayBlanksAs(DataSeries::EMPTY_AS_ZERO);
        self::assertSame(DataSeries::EMPTY_AS_ZERO, $chart1->getDisplayBlanksAs());
        $chart1->setDisplayBlanksAs('0');
        self::assertSame(DataSeries::EMPTY_AS_GAP, $chart1->getDisplayBlanksAs(), 'invalid setting converted to default');

        $chart2 = new Chart(
            'chart2', // name
            $title, // title
            $legend, // legend
            $plotArea, // plotArea
            true, // plotVisibleOnly
            DataSeries::EMPTY_AS_SPAN, // displayBlanksAs
            null, // xAxisLabel
            $yAxisLabel  // yAxisLabel
        );
        self::assertSame(DataSeries::EMPTY_AS_SPAN, $chart2->getDisplayBlanksAs());

        $chart3 = new Chart(
            'chart3', // name
            $title, // title
            $legend, // legend
            $plotArea, // plotArea
            true, // plotVisibleOnly
            '0', // displayBlanksAs, PHPExcel default
            null, // xAxisLabel
            $yAxisLabel  // yAxisLabel
        );
        self::assertSame(DataSeries::EMPTY_AS_GAP, $chart3->getDisplayBlanksAs(), 'invalid setting converted to default');

        $spreadsheet->disconnectWorksheets();
    }
}
