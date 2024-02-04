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
use PhpOffice\PhpSpreadsheet\Chart\TrendLine;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

require __DIR__ . '/../Header.php';

$spreadsheet = new Spreadsheet();
$dataSheet = $spreadsheet->getActiveSheet();
$dataSheet->setTitle('Data');
// changed data to simulate a trend chart - Xaxis are dates; Yaxis are 3 meausurements from each date
$dataSheet->fromArray(
    [
        ['', 'metric1', 'metric2', 'metric3'],
        ['=DATEVALUE("2021-01-01")', 12.1, 15.1, 21.1],
        ['=DATEVALUE("2021-04-01")', 56.2, 73.2, 86.2],
        ['=DATEVALUE("2021-07-01")', 52.2, 61.2, 69.2],
        ['=DATEVALUE("2021-10-01")', 30.2, 22.2, 0.2],
        ['=DATEVALUE("2022-01-01")', 40.1, 38.1, 65.1],
        ['=DATEVALUE("2022-04-01")', 45.2, 44.2, 96.2],
        ['=DATEVALUE("2022-07-01")', 52.2, 51.2, 55.2],
        ['=DATEVALUE("2022-10-01")', 41.2, 72.2, 56.2],
    ]
);

$dataSheet->getStyle('A2:A9')->getNumberFormat()->setFormatCode(Properties::FORMAT_CODE_DATE_ISO8601);
$dataSheet->getColumnDimension('A')->setAutoSize(true);
$dataSheet->setSelectedCells('A1');

// Set the Labels for each data series we want to plot
//     Datatype
//     Cell reference for data
//     Format Code
//     Number of datapoints in series
$dataSeriesLabels = [
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Data!$B$1', null, 1),
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Data!$C$1', null, 1),
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Data!$D$1', null, 1),
];
// Set the X-Axis Labels
// NUMBER, not STRING
// added x-axis values for each of the 3 metrics
// added FORMATE_CODE_NUMBER
// Number of datapoints in series
$xAxisTickValues = [
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Data!$A$2:$A$9', Properties::FORMAT_CODE_DATE_ISO8601, 8),
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Data!$A$2:$A$9', Properties::FORMAT_CODE_DATE_ISO8601, 8),
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Data!$A$2:$A$9', Properties::FORMAT_CODE_DATE_ISO8601, 8),
];
// Set the Data values for each data series we want to plot
//     Datatype
//     Cell reference for data
//     Format Code
//     Number of datapoints in series
//     Data values
//     Data Marker
//     Data Marker Color fill/[fill,Border]
//     Data Marker size
//   Color(s) added
// added FORMAT_CODE_NUMBER
$dataSeriesValues = [
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Data!$B$2:$B$9', Properties::FORMAT_CODE_NUMBER, 8, null, 'diamond', null, 5),
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Data!$C$2:$C$9', Properties::FORMAT_CODE_NUMBER, 8, null, 'square', '*accent1', 6),
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Data!$D$2:$D$9', Properties::FORMAT_CODE_NUMBER, 8, null, null, null, 7), // let Excel choose marker shape
];
// series 1 - metric1
// marker details
$dataSeriesValues[0]
    ->getMarkerFillColor()
    ->setColorProperties('0070C0', null, ChartColor::EXCEL_COLOR_TYPE_RGB);
$dataSeriesValues[0]
    ->getMarkerBorderColor()
    ->setColorProperties('002060', null, ChartColor::EXCEL_COLOR_TYPE_RGB);

// line details - dashed, smooth line (Bezier) with arrows, 40% transparent
$dataSeriesValues[0]
    ->setSmoothLine(true)
    ->setScatterLines(true)
    ->setLineColorProperties('accent1', 40, ChartColor::EXCEL_COLOR_TYPE_SCHEME); // value, alpha, type
$dataSeriesValues[0]->setLineStyleProperties(
    2.5, // width in points
    Properties::LINE_STYLE_COMPOUND_TRIPLE, // compound
    Properties::LINE_STYLE_DASH_SQUARE_DOT, // dash
    Properties::LINE_STYLE_CAP_SQUARE, // cap
    Properties::LINE_STYLE_JOIN_MITER, // join
    Properties::LINE_STYLE_ARROW_TYPE_OPEN, // head type
    Properties::LINE_STYLE_ARROW_SIZE_4, // head size preset index
    Properties::LINE_STYLE_ARROW_TYPE_ARROW, // end type
    Properties::LINE_STYLE_ARROW_SIZE_6 // end size preset index
);

// series 2 - metric2, straight line - no special effects, connected
$dataSeriesValues[1] // square marker border color
    ->getMarkerBorderColor()
    ->setColorProperties('accent6', 3, ChartColor::EXCEL_COLOR_TYPE_SCHEME);
$dataSeriesValues[1] // square marker fill color
    ->getMarkerFillColor()
    ->setColorProperties('0FFF00', null, ChartColor::EXCEL_COLOR_TYPE_RGB);
$dataSeriesValues[1]
    ->setScatterLines(true)
    ->setSmoothLine(false)
    ->setLineColorProperties('FF0000', 80, ChartColor::EXCEL_COLOR_TYPE_RGB);
$dataSeriesValues[1]->setLineWidth(2.0);

// series 3 - metric3, markers, no line
$dataSeriesValues[2] // triangle? fill
    //->setPointMarker('triangle') // let Excel choose shape, which is predicted to be a triangle
    ->getMarkerFillColor()
    ->setColorProperties('FFFF00', null, ChartColor::EXCEL_COLOR_TYPE_RGB);
$dataSeriesValues[2] // triangle border
    ->getMarkerBorderColor()
    ->setColorProperties('accent4', null, ChartColor::EXCEL_COLOR_TYPE_SCHEME);
$dataSeriesValues[2]->setScatterLines(false); // points not connected
// Added so that Xaxis shows dates instead of Excel-equivalent-year1900-numbers
$xAxis = new ChartAxis();
$xAxis->setAxisNumberProperties(Properties::FORMAT_CODE_DATE_ISO8601, true);

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
    DataSeries::STYLE_SMOOTHMARKER // plotStyle
);

// Set the series in the plot area
$plotArea = new PlotArea(null, [$series]);
// Set the chart legend
$legend = new ChartLegend(ChartLegend::POSITION_TOPRIGHT, null, false);

$title = new Title('Test Scatter Chart');
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
    $yAxisLabel,  // yAxisLabel
    // added xAxis for correct date display
    $xAxis, // xAxis
    //  $yAxis, // yAxis
);

// Set the position of the chart in the chart sheet
$chart->setTopLeftPosition('A1');
$chart->setBottomRightPosition('P12');

// create a 'Chart' worksheet, add $chart to it
$spreadsheet->createSheet();
$chartSheet = $spreadsheet->getSheet(1);
$chartSheet->setTitle('Scatter Chart');

$chartSheet = $spreadsheet->getSheetByNameOrThrow('Scatter Chart');
// Add the chart to the worksheet
$chartSheet->addChart($chart);

$helper->renderChart($chart, __FILE__);

// ------------ Demonstrate Trendlines for metric3 values in a new chart ------------

$dataSeriesLabels = [
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Data!$D$1', null, 1),
];
$xAxisTickValues = [
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Data!$A$2:$A$9', Properties::FORMAT_CODE_DATE_ISO8601, 8),
];

$dataSeriesValues = [
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Data!$D$2:$D$9', Properties::FORMAT_CODE_NUMBER, 4, null, 'triangle', null, 7),
];

// add 3 trendlines:
// 1- linear, double-ended arrow, w=0.5, same color as marker fill; nodispRSqr, nodispEq
// 2- polynomial (order=3) no-arrow trendline, w=1.25, same color as marker fill; dispRSqr, dispEq
// 3- moving Avg (period=2) single-arrow trendline, w=1.5, same color as marker fill; no dispRSqr, no dispEq
$trendLines = [
    new TrendLine(TrendLine::TRENDLINE_LINEAR, null, null, false, false),
    new TrendLine(TrendLine::TRENDLINE_POLYNOMIAL, 3, null, true, true, 20.0, 28.0, 44104.5, 'metric3 polynomial fit'),
    new TrendLine(TrendLine::TRENDLINE_MOVING_AVG, null, 2, true),
];
$dataSeriesValues[0]->setTrendLines($trendLines);

// Suppress connecting lines; instead, add different Trendline algorithms to
// determine how well the data fits the algorithm (Rsquared="goodness of fit")
// Display RSqr plus the eqn just because we can.

$dataSeriesValues[0]->setScatterLines(false); // points not connected
$dataSeriesValues[0]->getMarkerFillColor()
    ->setColorProperties('FFFF00', null, ChartColor::EXCEL_COLOR_TYPE_RGB);
$dataSeriesValues[0]->getMarkerBorderColor()
    ->setColorProperties('accent4', null, ChartColor::EXCEL_COLOR_TYPE_SCHEME);

// add properties to the trendLines - give each a different color
$dataSeriesValues[0]->getTrendLines()[0]->getLineColor()->setColorProperties('accent4', null, ChartColor::EXCEL_COLOR_TYPE_SCHEME);
$dataSeriesValues[0]->getTrendLines()[0]->setLineStyleProperties(0.5, null, null, null, null, Properties::LINE_STYLE_ARROW_TYPE_STEALTH, 5, Properties::LINE_STYLE_ARROW_TYPE_OPEN, 8);

$dataSeriesValues[0]->getTrendLines()[1]->getLineColor()->setColorProperties('accent3', null, ChartColor::EXCEL_COLOR_TYPE_SCHEME);
$dataSeriesValues[0]->getTrendLines()[1]->setLineStyleProperties(1.25);

$dataSeriesValues[0]->getTrendLines()[2]->getLineColor()->setColorProperties('accent2', null, ChartColor::EXCEL_COLOR_TYPE_SCHEME);
$dataSeriesValues[0]->getTrendLines()[2]->setLineStyleProperties(1.5, null, null, null, null, null, 0, Properties::LINE_STYLE_ARROW_TYPE_OPEN, 8);

$xAxis = new ChartAxis();
$xAxis->setAxisNumberProperties(Properties::FORMAT_CODE_DATE_ISO8601); // m/d/yyyy

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
    DataSeries::STYLE_SMOOTHMARKER // plotStyle
);

// Set the series in the plot area
$plotArea = new PlotArea(null, [$series]);
// Set the chart legend
$legend = new ChartLegend(ChartLegend::POSITION_TOPRIGHT, null, false);

$title = new Title('Test Scatter Chart - trendlines for metric3 values');
$yAxisLabel = new Title('Value ($k)');

// Create the chart
$chart = new Chart(
    'chart2', // name
    $title, // title
    $legend, // legend
    $plotArea, // plotArea
    true, // plotVisibleOnly
    DataSeries::EMPTY_AS_GAP, // displayBlanksAs
    null, // xAxisLabel
    $yAxisLabel,  // yAxisLabel
    // added xAxis for correct date display
    $xAxis, // xAxis
    //  $yAxis, // yAxis
);

// Set the position of the chart in the chart sheet below the first chart
$chart->setTopLeftPosition('A13');
$chart->setBottomRightPosition('P25');

// Add the chart to the worksheet $chartSheet
$chartSheet->addChart($chart);

$helper->renderChart($chart, __FILE__);

$spreadsheet->setActiveSheetIndex(1);

// Save Excel 2007 file
$helper->write($spreadsheet, __FILE__, ['Xlsx'], true, resetActiveSheet: false);
