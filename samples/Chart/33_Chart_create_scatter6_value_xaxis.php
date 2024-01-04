<?php

use PhpOffice\PhpSpreadsheet\Chart\Axis as ChartAxis;
use PhpOffice\PhpSpreadsheet\Chart\Chart;
use PhpOffice\PhpSpreadsheet\Chart\ChartColor;
use PhpOffice\PhpSpreadsheet\Chart\DataSeries;
use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
use PhpOffice\PhpSpreadsheet\Chart\Legend as ChartLegend;
use PhpOffice\PhpSpreadsheet\Chart\PlotArea;
use PhpOffice\PhpSpreadsheet\Chart\Properties;
use PhpOffice\PhpSpreadsheet\Chart\Title;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

require __DIR__ . '/../Header.php';

$spreadsheet = new Spreadsheet();
$dataSheet = $spreadsheet->getActiveSheet();
$dataSheet->setTitle('Data');

$results = [
    ['Station 1', 'Score'],
    [13.25, 3],
    [16.25, 4],
    [18.5, 4],
    [15.5, 3],
    [15.75, 5],
    [17.25, 4],
    [10.5, 2],
];

$dataSheet->fromArray($results);

$spreadsheet->createSheet();

$chartSheet = $spreadsheet->getSheet(1);
$chartSheet->setTitle('Appendix');

$dataSeriesLabels = [
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Data!$A$1', null, 1),
];

$dataSeriesValues = [
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Data!$A$2:$A$' . count($results), Properties::FORMAT_CODE_NUMBER, 4, null, 'diamond', null, 7),
];

$xAxisTickValues = [
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Data!$B$2:$B$' . count($results), Properties::FORMAT_CODE_NUMBER, 8),
];

$dataSeriesValues[0]->setScatterLines(false); // Points not connected

$dataSeriesValues[0]->getMarkerFillColor()
    ->setColorProperties('accent1', null, ChartColor::EXCEL_COLOR_TYPE_SCHEME);

// Build the dataseries
$series = new DataSeries(
    DataSeries::TYPE_SCATTERCHART, // plotType
    null, // plotGrouping (Scatter charts don't have grouping)
    range(0, count($dataSeriesValues) - 1), // plotOrder
    $dataSeriesLabels, // plotLabel
    $xAxisTickValues, // plotCategory
    $dataSeriesValues, // plotValues
    null, // plotDirection
    false, // smooth line
    DataSeries::STYLE_LINEMARKER // plotStyle
);

// Set the series in the plot area
$plotArea = new PlotArea(null, [$series]);
// Set the chart legend
$legend = new ChartLegend(ChartLegend::POSITION_TOPRIGHT, null, false);

$title = new Title($results[0][0]);

$xAxis = new ChartAxis();

$xAxis->setAxisOptionsProperties(
    Properties::AXIS_LABELS_NEXT_TO,
    null, // horizontalCrossesValue
    null, // horizontalCrosses
    null, // axisOrientation
    null, // majorTmt
    Properties::TICK_MARK_OUTSIDE, // minorTmt
    '0', // minimum
    '6', // maximum
    null, // majorUnit
    '1', // minorUnit
);

$xAxis->setAxisType(ChartAxis::AXIS_TYPE_VALUE);

$yAxis = new ChartAxis();

$yAxis->setAxisOptionsProperties(
    Properties::AXIS_LABELS_NEXT_TO,
    null, // horizontalCrossesValue
    null, // horizontalCrosses
    null, // axisOrientation
    null, // majorTmt
    Properties::TICK_MARK_OUTSIDE, // minorTmt
    '0', // minimum
    '25', // 30 // maximum
    null, // majorUnit
    '5', // minorUnit
);

// Create the chart
$chart = new Chart(
    'chart2', // name
    $title, // title
    $legend, // legend
    $plotArea, // plotArea
    true, // plotVisibleOnly
    DataSeries::EMPTY_AS_GAP, // displayBlanksAs
    null, // xAxisLabel
    null, // yAxisLabel
    // added xAxis for correct date display
    $xAxis, // xAxis
    $yAxis, // yAxis
);

// Set the position of the chart in the chart sheet below the first chart
$chart->setTopLeftPosition('B2');
$chart->setBottomRightPosition('K22');

// Add the chart to the worksheet $chartSheet
$chartSheet->addChart($chart);

$helper->renderChart($chart, __FILE__);

$spreadsheet->setActiveSheetIndex(1);

// Save Excel 2007 file
$helper->write($spreadsheet, __FILE__, ['Xlsx'], true, resetActiveSheet: false);
