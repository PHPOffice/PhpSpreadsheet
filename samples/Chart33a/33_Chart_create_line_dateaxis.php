<?php

namespace PhpOffice\PhpSpreadsheet\Chart;

use DateTime;
use PhpOffice\PhpSpreadsheet\Chart\Legend as ChartLegend;
use PhpOffice\PhpSpreadsheet\Shared\Date as SharedDate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

require __DIR__ . '/../Header.php';

$spreadsheet = new Spreadsheet();
$dataSheet = $spreadsheet->getActiveSheet();
$dataSheet->setTitle('Data');
// changed data to simulate a trend chart - Xaxis are dates; Yaxis are 3 meausurements from each date
// Dates changed not to fall on exact quarter start
$dataSheet->fromArray(
    [
        ['', 'date', 'metric1', 'metric2', 'metric3'],
        ['=DATEVALUE(B2)', '2021-01-10', 12.1, 15.1, 21.1],
        ['=DATEVALUE(B3)', '2021-04-21', 56.2, 73.2, 86.2],
        ['=DATEVALUE(B4)', '2021-07-31', 52.2, 61.2, 69.2],
        ['=DATEVALUE(B5)', '2021-10-11', 30.2, 22.2, 0.2],
        ['=DATEVALUE(B6)', '2022-01-21', 40.1, 38.1, 65.1],
        ['=DATEVALUE(B7)', '2022-04-11', 45.2, 44.2, 96.2],
        ['=DATEVALUE(B8)', '2022-07-01', 52.2, 51.2, 55.2],
        ['=DATEVALUE(B9)', '2022-10-31', 41.2, 72.2, 56.2],
    ]
);

$dataSheet->getStyle('A2:A9')->getNumberFormat()->setFormatCode(Properties::FORMAT_CODE_DATE_ISO8601);
$dataSheet->getColumnDimension('A')->setAutoSize(true);
$dataSheet->getColumnDimension('B')->setAutoSize(true);
$dataSheet->setSelectedCells('A1');

// Set the Labels for each data series we want to plot
//     Datatype
//     Cell reference for data
//     Format Code
//     Number of datapoints in series
$dataSeriesLabels = [
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Data!$C$1', null, 1),
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Data!$D$1', null, 1),
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Data!$E$1', null, 1),
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
    new DataSeriesValues(
        DataSeriesValues::DATASERIES_TYPE_NUMBER,
        'Data!$C$2:$C$9',
        Properties::FORMAT_CODE_NUMBER,
        8,
        null,
        'diamond',
        null,
        5
    ),
    new DataSeriesValues(
        DataSeriesValues::DATASERIES_TYPE_NUMBER,
        'Data!$D$2:$D$9',
        Properties::FORMAT_CODE_NUMBER,
        8,
        null,
        'square',
        '*accent1',
        6
    ),
    new DataSeriesValues(
        DataSeriesValues::DATASERIES_TYPE_NUMBER,
        'Data!$E$2:$E$9',
        Properties::FORMAT_CODE_NUMBER,
        8,
        null,
        null,
        null,
        7
    ), // let Excel choose marker shape
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
$xAxis = new Axis();
$xAxis->setAxisNumberProperties(Properties::FORMAT_CODE_DATE_ISO8601);

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
$yAxis = new Axis();
$yAxis->setMajorGridlines(new GridLines());

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
    $yAxis, // yAxis
);

// Set the position of the chart in the chart sheet
$chart->setTopLeftPosition('A1');
$chart->setBottomRightPosition('P12');

// create a 'Chart' worksheet, add $chart to it
$spreadsheet->createSheet();
$chartSheet = $spreadsheet->getSheet(1);
$chartSheet->setTitle('Scatter+Line Chart');

$chartSheet = $spreadsheet->getSheetByNameOrThrow('Scatter+Line Chart');
// Add the chart to the worksheet
$chartSheet->addChart($chart);

// ------- Demonstrate Date Xaxis in Line Chart, not possible using Scatter Chart ------------

// Set the Labels (Column header) for each data series we want to plot
$dataSeriesLabels = [
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Data!$E$1', null, 1),
];

// Set the X-Axis Labels - dates, N.B. 01/10/2021 === Jan 10, NOT Oct 1 !!
// x-axis values are the Excel numeric representation of the date - so set
// formatCode=General for the xAxis VALUES, but we want the labels to be
// DISPLAYED as 'yyyy-mm-dd'  That is, read a number, display a date.
$xAxisTickValues = [
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Data!$A$2:$A$9', Properties::FORMAT_CODE_DATE_ISO8601, 8),
];

// X axis (date) settings
$xAxisLabel = new Title('Date');
$xAxis = new Axis();
$xAxis->setAxisNumberProperties(Properties::FORMAT_CODE_DATE_ISO8601); // yyyy-mm-dd

// Set the Data values for each data series we want to plot
$dataSeriesValues = [
    new DataSeriesValues(
        DataSeriesValues::DATASERIES_TYPE_NUMBER,
        'Data!$E$2:$E$9',
        Properties::FORMAT_CODE_NUMBER,
        8,
        null,
        'triangle',
        null,
        7
    ),
];

// series - metric3, markers, no line
$dataSeriesValues[0]
    ->setScatterlines(false); // disable connecting lines
$dataSeriesValues[0]
    ->getMarkerFillColor()
    ->setColorProperties('FFFF00', null, ChartColor::EXCEL_COLOR_TYPE_RGB);
$dataSeriesValues[0]
    ->getMarkerBorderColor()
    ->setColorProperties('accent4', null, ChartColor::EXCEL_COLOR_TYPE_SCHEME);

// Build the dataseries
// must now use LineChart instead of ScatterChart, since ScatterChart does not
// support "dateAx" axis type.
$series = new DataSeries(
    DataSeries::TYPE_LINECHART, // plotType
    'standard', // plotGrouping
    range(0, count($dataSeriesValues) - 1), // plotOrder
    $dataSeriesLabels, // plotLabel
    $xAxisTickValues, // plotCategory
    $dataSeriesValues, // plotValues
    null, // plotDirection
    false, // smooth line
    DataSeries::STYLE_LINEMARKER  // plotStyle
    // DataSeries::STYLE_SMOOTHMARKER // plotStyle
);

// Set the series in the plot area
$plotArea = new PlotArea(null, [$series]);
// Set the chart legend
$legend = new ChartLegend(ChartLegend::POSITION_RIGHT, null, false);

$title = new Title('Test Line-Chart with Date Axis - metric3 values');

// X axis (date) settings
$xAxisLabel = new Title('Game Date');
$xAxis = new Axis();
// date axis values are Excel numbers, not yyyy-mm-dd Date strings
$xAxis->setAxisNumberProperties(Properties::FORMAT_CODE_DATE_ISO8601);

$xAxis->setAxisType('dateAx'); // dateAx available ONLY for LINECHART, not SCATTERCHART

// measure the time span in Quarters, of data.
$dateMinMax = dateRange(8, $spreadsheet); // array 'min'=>earliest date of first Q, 'max'=>latest date of final Q
// change xAxis tick marks to match Qtr boundaries

$nQtrs = sprintf('%3.2f', (($dateMinMax['max'] - $dateMinMax['min']) / 30.5) / 4);
$tickMarkInterval = ($nQtrs > 20) ? '6' : '3'; // tick marks every ? months

$xAxis->setAxisOptionsProperties(
    Properties::AXIS_LABELS_NEXT_TO, // axis_label pos
    null, // horizontalCrossesValue
    null, // horizontalCrosses
    null, // axisOrientation
    'in', // major_tick_mark
    null, // minor_tick_mark
    $dateMinMax['min'], // minimum calculate this from the earliest data: 'Data!$A$2'
    $dateMinMax['max'], // maximum calculate this from the last data:     'Data!$A$'.($nrows+1)
    $tickMarkInterval,  // majorUnit determines tickmarks & Gridlines ?
    null, // minorUnit
    null, // textRotation
    null, // hidden
    'days', // baseTimeUnit
    'months', // majorTimeUnit,
    'months',   // minorTimeUnit
);

$yAxisLabel = new Title('Value ($k)');
$yAxis = new Axis();
$yAxis->setMajorGridlines(new GridLines());
$xAxis->setMajorGridlines(new GridLines());
$minorGridLines = new GridLines();
$minorGridLines->activateObject();
$xAxis->setMinorGridlines($minorGridLines);

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
    $yAxis, // yAxis
);

// Set the position of the chart in the chart sheet below the first chart
$chart->setTopLeftPosition('A13');
$chart->setBottomRightPosition('P25');
$chart->setRoundedCorners(true); // Rounded corners in Chart Outline

// Add the chart to the worksheet $chartSheet
$chartSheet->addChart($chart);

$helper->renderChart($chart, __FILE__);

$spreadsheet->setActiveSheetIndex(1);

// Save Excel 2007 file
$helper->write($spreadsheet, __FILE__, ['Xlsx'], true, resetActiveSheet: false);
$spreadsheet->disconnectWorksheets();

function dateRange(int $nrows, Spreadsheet $wrkbk): array
{
    $dataSheet = $wrkbk->getSheetByNameOrThrow('Data');

    // start the xaxis at the beginning of the quarter of the first date
    /** @var string */
    $startDateStr = $dataSheet->getCell('B2')->getValue(); // yyyy-mm-dd date string
    $startDate = DateTime::createFromFormat('Y-m-d', $startDateStr); // php date obj
    if ($startDate === false) {
        throw new Exception("invalid start date $startDateStr on spreadsheet");
    }

    // get date of first day of the quarter of the start date
    $startMonth = (int) $startDate->format('n'); // suppress leading zero
    $startYr = (int) $startDate->format('Y');
    $qtr = intdiv($startMonth, 3) + (($startMonth % 3 > 0) ? 1 : 0);
    $qtrStartMonth = sprintf('%02d', 1 + (($qtr - 1) * 3));
    $qtrStartStr = "$startYr-$qtrStartMonth-01";
    $ExcelQtrStartDateVal = SharedDate::convertIsoDate($qtrStartStr);

    // end the xaxis at the end of the quarter of the last date
    /** @var string */
    $lastDateStr = $dataSheet->getCell([2, $nrows + 1])->getValue();
    $lastDate = DateTime::createFromFormat('Y-m-d', $lastDateStr);
    if ($lastDate === false) {
        throw new Exception("invalid last date $lastDateStr on spreadsheet");
    }
    $lastMonth = (int) $lastDate->format('n');
    $lastYr = (int) $lastDate->format('Y');
    $qtr = intdiv($lastMonth, 3) + (($lastMonth % 3 > 0) ? 1 : 0);
    $qtrEndMonth = 3 + (($qtr - 1) * 3);
    $qtrEndMonth = sprintf('%02d', $qtrEndMonth);
    $lastDOMDate = DateTime::createFromFormat('Y-m-d', "$lastYr-$qtrEndMonth-01");
    if ($lastDOMDate === false) {
        throw new Exception("invalid last dom date $lastYr-$qtrEndMonth-01 on spreadsheet");
    }
    $lastDOM = $lastDOMDate->format('t');
    $qtrEndStr = "$lastYr-$qtrEndMonth-$lastDOM";
    $ExcelQtrEndDateVal = SharedDate::convertIsoDate($qtrEndStr);

    $minMaxDates = ['min' => $ExcelQtrStartDateVal, 'max' => $ExcelQtrEndDateVal];

    return $minMaxDates;
}
