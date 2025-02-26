<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Chart;

use PhpOffice\PhpSpreadsheet\Chart\Chart;
use PhpOffice\PhpSpreadsheet\Chart\DataSeries;
use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
use PhpOffice\PhpSpreadsheet\Chart\PlotArea;
use PhpOffice\PhpSpreadsheet\Exception as SpreadsheetException;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

class ChartsByNameTest extends TestCase
{
    public function testChartByName(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Only Sheet');
        $sheet->fromArray(
            [
                ['Some Title'],
                [],
                [null, null, 'Data'],
                [null, 'L1', 1.3],
                [null, 'L2', 1.3],
                [null, 'L3', 2.3],
                [null, 'L4', 1.6],
                [null, 'L5', 1.5],
                [null, 'L6', 1.4],
                [null, 'L7', 2.2],
                [null, 'L8', 1.8],
                [null, 'L9', 1.1],
                [null, 'L10', 1.8],
                [null, 'L11', 1.6],
                [null, 'L12', 2.7],
                [null, 'L13', 2.2],
                [null, 'L14', 1.3],
            ]
        );

        $dataSeriesLabels = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, '\'Only Sheet\'!$B$4', null, 1), // 2010
        ];
        // Set the X-Axis Labels
        $xAxisTickValues = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, '\'Only Sheet\'!$B$4:$B$17'),
        ];
        // Set the Data values for each data series we want to plot
        $dataSeriesValues = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, '\'Only Sheet\'!$C$4:$C$17'),
        ];

        // Build the dataseries
        $series = new DataSeries(
            DataSeries::TYPE_BARCHART, // plotType
            DataSeries::GROUPING_STANDARD, // plotGrouping
            range(0, count($dataSeriesValues) - 1), // plotOrder
            $dataSeriesLabels, // plotLabel
            $xAxisTickValues, // plotCategory
            $dataSeriesValues, // plotValues
        );

        // Set the series in the plot area
        $plotArea = new PlotArea(null, [$series]);

        // Create the chart
        $chart = new Chart(
            name: 'namedchart1',
            plotArea: $plotArea,
        );

        // Set the position where the chart should appear in the worksheet
        $chart->setTopLeftPosition('G7');
        $chart->setBottomRightPosition('N21');
        // Add the chart to the worksheet
        $sheet->addChart($chart);
        $sheet->setSelectedCells('D1');
        self::assertSame($chart, $sheet->getChartByName('namedchart1'));
        self::assertSame($chart, $sheet->getChartByNameOrThrow('namedchart1'));
        self::assertFalse($sheet->getChartByName('namedchart2'));

        try {
            $sheet->getChartByNameOrThrow('namedchart2');
            $exceptionRaised = false;
        } catch (SpreadsheetException $e) {
            self::assertSame('Sheet does not have a chart named namedchart2.', $e->getMessage());
            $exceptionRaised = true;
        }

        self::assertTrue($exceptionRaised);

        $spreadsheet->disconnectWorksheets();
    }
}
