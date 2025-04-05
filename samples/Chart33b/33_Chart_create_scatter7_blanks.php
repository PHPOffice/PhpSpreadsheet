<?php

use PhpOffice\PhpSpreadsheet\Chart\Chart;
use PhpOffice\PhpSpreadsheet\Chart\DataSeries;
use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
use PhpOffice\PhpSpreadsheet\Chart\Legend as ChartLegend;
use PhpOffice\PhpSpreadsheet\Chart\PlotArea;
use PhpOffice\PhpSpreadsheet\Chart\Title;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

require __DIR__ . '/../Header.php';
/** @var PhpOffice\PhpSpreadsheet\Helper\Sample $helper */
$spreadsheet = new Spreadsheet();
$worksheet = $spreadsheet->getActiveSheet();
$worksheet->fromArray(
    [
        ['', 2010, 2011, 2012],
        ['Q1', 12, 15, 21],
        ['Q2', 56, null, 86],
        ['Q3', 52, 61, 69],
        ['Q4', 30, 32, 0],
    ],
    strictNullComparison: true
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
    DataSeries::TYPE_SCATTERCHART, // plotType
    null, // plotGrouping (Scatter charts don't have any grouping)
    range(0, count($dataSeriesValues) - 1), // plotOrder
    $dataSeriesLabels, // plotLabel
    $xAxisTickValues, // plotCategory
    $dataSeriesValues, // plotValues
    null, // plotDirection
    false, // smooth line
    DataSeries::STYLE_LINEMARKER  // plotStyle
);

// Set the series in the plot area
$plotArea = new PlotArea(null, [$series]);
// Set the chart legend
$legend = new ChartLegend(ChartLegend::POSITION_TOPRIGHT, null, false);

$title1 = new Title('Test Scatter Chart Gap');
$yAxisLabel1 = new Title('Value ($k)');
// Create the chart
$chart1 = new Chart(
    'chart1', // name
    $title1, // title
    $legend, // legend
    $plotArea, // plotArea
    true, // plotVisibleOnly
    DataSeries::EMPTY_AS_GAP, // displayBlanksAs
    null, // xAxisLabel
    $yAxisLabel1  // yAxisLabel
);

// Set the position where the chart should appear in the worksheet
$chart1->setTopLeftPosition('A7');
$chart1->setBottomRightPosition('H20');

// Add the chart to the worksheet
$worksheet->addChart($chart1);

$helper->renderChart($chart1, __FILE__);

$title2 = new Title('Test Scatter Chart Zero');
$yAxisLabel2 = new Title('Value ($k)');
// Create the chart
$chart2 = new Chart(
    'chart2', // name
    $title2, // title
    $legend, // legend
    $plotArea, // plotArea
    true, // plotVisibleOnly
    DataSeries::EMPTY_AS_ZERO, // displayBlanksAs
    null, // xAxisLabel
    $yAxisLabel2  // yAxisLabel
);

// Set the position where the chart should appear in the worksheet
$chart2->setTopLeftPosition('A22');
$chart2->setBottomRightPosition('H35');

// Add the chart to the worksheet
$worksheet->addChart($chart2);

$helper->renderChart($chart2, __FILE__);

$title3 = new Title('Test Scatter Chart Span');
$yAxisLabel3 = new Title('Value ($k)');

// Create the chart
$chart3 = new Chart(
    'chart3', // name
    $title3, // title
    $legend, // legend
    $plotArea, // plotArea
    true, // plotVisibleOnly
    DataSeries::EMPTY_AS_SPAN, // displayBlanksAs
    null, // xAxisLabel
    $yAxisLabel3  // yAxisLabel
);

// Set the position where the chart should appear in the worksheet
$chart3->setTopLeftPosition('A37');
$chart3->setBottomRightPosition('H50');

// Add the chart to the worksheet
$worksheet->addChart($chart3);

$helper->renderChart($chart3, __FILE__);

// Save Excel 2007 file
$helper->write($spreadsheet, __FILE__, ['Xlsx'], true);
