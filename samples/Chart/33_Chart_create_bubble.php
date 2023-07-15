<?php

use PhpOffice\PhpSpreadsheet\Chart\Chart;
use PhpOffice\PhpSpreadsheet\Chart\DataSeries;
use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
use PhpOffice\PhpSpreadsheet\Chart\Legend as ChartLegend;
use PhpOffice\PhpSpreadsheet\Chart\PlotArea;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

require __DIR__ . '/../Header.php';

$spreadsheet = new Spreadsheet();
$worksheet = $spreadsheet->getActiveSheet();
$worksheet->fromArray(
    [
        ['Number of Products', 'Sales in USD', 'Market share'],
        [14, 12200, 15],
        [20, 60000, 33],
        [18, 24400, 10],
        [22, 32000, 42],
        [],
        [12, 8200, 18],
        [15, 50000, 30],
        [19, 22400, 15],
        [25, 25000, 50],
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
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, null, null, 1, ['2013']), // 2013
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, null, null, 1, ['2014']), // 2014
];

// Set the X-Axis values
//     Datatype
//     Cell reference for data
//     Format Code
//     Number of datapoints in series
//     Data values
//     Data Marker
$dataSeriesCategories = [
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$A$2:$A$5', null, 4),
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$A$7:$A$10', null, 4),
];

// Set the Y-Axis values
//     Datatype
//     Cell reference for data
//     Format Code
//     Number of datapoints in series
//     Data values
//     Data Marker
$dataSeriesValues = [
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$B$2:$B$5', null, 4),
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$B$7:$B$10', null, 4),
];

// Set the Z-Axis values (bubble size)
//     Datatype
//     Cell reference for data
//     Format Code
//     Number of datapoints in series
//     Data values
//     Data Marker
$dataSeriesBubbles = [
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$C$2:$C$5', null, 4),
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$C$7:$C$10', null, 4),
];

// Build the dataseries
$series = new DataSeries(
    DataSeries::TYPE_BUBBLECHART, // plotType
    null, // plotGrouping
    range(0, count($dataSeriesValues) - 1), // plotOrder
    $dataSeriesLabels, // plotLabel
    $dataSeriesCategories, // plotCategory
    $dataSeriesValues // plotValues
);
$series->setPlotBubbleSizes($dataSeriesBubbles);

// Set the series in the plot area
$plotArea = new PlotArea();
$plotArea->setPlotSeries([$series]);
// Set the chart legend
$legend = new ChartLegend(ChartLegend::POSITION_RIGHT, null, false);

// Create the chart
$chart = new Chart(
    'chart1', // name
    null, // title
    $legend, // legend
    $plotArea, // plotArea
    true, // plotVisibleOnly
    DataSeries::EMPTY_AS_GAP, // displayBlanksAs
    null, // xAxisLabel
    null // yAxisLabel
);

// Set the position where the chart should appear in the worksheet
$chart->setTopLeftPosition('E1');
$chart->setBottomRightPosition('M15');

// Add the chart to the worksheet
$worksheet->addChart($chart);

$helper->renderChart($chart, __FILE__);

$worksheet->getColumnDimension('A')->setAutoSize(true);
$worksheet->getColumnDimension('B')->setAutoSize(true);
$worksheet->getColumnDimension('C')->setAutoSize(true);

// Save Excel 2007 file
$helper->write($spreadsheet, __FILE__, ['Xlsx'], true);
