<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Chart;

use PhpOffice\PhpSpreadsheet\Chart\Chart;
use PhpOffice\PhpSpreadsheet\Chart\DataSeries;
use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
use PhpOffice\PhpSpreadsheet\Chart\Exception as ChartException;
use PhpOffice\PhpSpreadsheet\Chart\PlotArea;
use PHPUnit\Framework\TestCase;

class PlotAreaTest extends TestCase
{
    public function testPlotArea(): void
    {
        $dataSeriesValues = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, null, null, 4, [1, 2, 3, 4]),
        ];

        // Build the dataseries
        $series = new DataSeries(
            plotType: DataSeries::TYPE_AREACHART,
            plotGrouping: DataSeries::GROUPING_PERCENT_STACKED,
            plotOrder: range(0, count($dataSeriesValues) - 1),
            plotValues: $dataSeriesValues
        );

        // Set the series in the plot area
        $plotArea = new PlotArea(null, [$series]);

        // Create the chart
        $chart = new Chart(
            'chart1', // name
            plotArea: $plotArea,
        );
        self::assertNotNull($chart->getPlotAreaOrThrow());
    }

    public function testNoPlotArea(): void
    {
        $chart = new Chart('chart1');
        $this->expectException(ChartException::class);
        $this->expectExceptionMessage('Chart has no PlotArea');
        $chart->getPlotAreaOrThrow();
    }
}
