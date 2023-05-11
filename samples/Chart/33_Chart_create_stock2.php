<?php

use PhpOffice\PhpSpreadsheet\Chart\AxisText;
use PhpOffice\PhpSpreadsheet\Chart\Chart;
use PhpOffice\PhpSpreadsheet\Chart\ChartColor;
use PhpOffice\PhpSpreadsheet\Chart\DataSeries;
use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
use PhpOffice\PhpSpreadsheet\Chart\Legend as ChartLegend;
use PhpOffice\PhpSpreadsheet\Chart\PlotArea;
use PhpOffice\PhpSpreadsheet\Chart\Title;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

require __DIR__ . '/../Header.php';

$spreadsheet = new Spreadsheet();
$worksheet = $spreadsheet->getActiveSheet();
$worksheet->fromArray(
    [
        ['Counts', 'Max', 'Min', 'Min Threshold', 'Max Threshold'],
        [10, 10, 5, 0, 50],
        [30, 20, 10, 0, 50],
        [20, 30, 15, 0, 50],
        [40, 10, 0, 0, 50],
        [100, 40, 5, 0, 50],
    ],
    null,
    'A1',
    true
);
$worksheet->getStyle('B2:E6')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_00);

// Set the Labels for each data series we want to plot
//     Datatype
//     Cell reference for data
//     Format Code
//     Number of datapoints in series
//     Data values
//     Data Marker
$dataSeriesLabels = [
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$B$1', null, 1), //Max / Open
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$C$1', null, 1), //Min / Close
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$D$1', null, 1), //Min Threshold / Min
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$E$1', null, 1), //Max Threshold / Max
];
// Set the X-Axis Labels
//     Datatype
//     Cell reference for data
//     Format Code
//     Number of datapoints in series
//     Data values
//     Data Marker
$xAxisTickValues = [
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$A$2:$A$6', null, 5), // Counts
];
// Set the Data values for each data series we want to plot
//     Datatype
//     Cell reference for data
//     Format Code
//     Number of datapoints in series
//     Data values
//     Data Marker
$dataSeriesValues = [
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$B$2:$B$6', null, 5),
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$C$2:$C$6', null, 5),
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$D$2:$D$6', null, 5),
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$E$2:$E$6', null, 5),
];

// Build the dataseries
$series = new DataSeries(
    DataSeries::TYPE_STOCKCHART, // plotType
    null, // plotGrouping - if we set this to not null, then xlsx throws error
    range(0, count($dataSeriesValues) - 1), // plotOrder
    $dataSeriesLabels, // plotLabel
    $xAxisTickValues, // plotCategory
    $dataSeriesValues       // plotValues
);

// Set the series in the plot area
$plotArea = new PlotArea(null, [$series]);
// Set the chart legend
$legend = new ChartLegend(ChartLegend::POSITION_RIGHT, null, false);
$legend->getBorderLines()->setLineColorProperties('ffc000', null, ChartColor::EXCEL_COLOR_TYPE_RGB);
$legend->getFillColor()->setColorProperties('cccccc');
$legendText = new AxisText();
$legendText->getFillColorObject()->setValue('008080')->setType(ChartColor::EXCEL_COLOR_TYPE_RGB);
$legendText->setShadowProperties(1);
$legend->setLegendText($legendText);

$title = new Title('Test Stock Chart');
$xAxisLabel = new Title('Counts');
$yAxisLabel = new Title('Values');

// 3 stmts below are only difference from 33_chart_create_stock.php
$plotArea->setGapWidth(300);
$plotArea->setUseUpBars(true);
$plotArea->setUseDownBars(true);

// Create the chart
$chart = new Chart(
    'stock-chart', // name
    $title, // title
    $legend, // legend
    $plotArea, // plotArea
    true, // plotVisibleOnly
    DataSeries::EMPTY_AS_GAP, // displayBlanksAs
    $xAxisLabel, // xAxisLabel
    $yAxisLabel  // yAxisLabel
);

// Set the position where the chart should appear in the worksheet
$chart->setTopLeftPosition('A7');
$chart->setBottomRightPosition('H20');

// Add the chart to the worksheet
$worksheet->addChart($chart);
$helper->renderChart($chart, __FILE__);
$worksheet->setSelectedCells('G2');

// Save Excel 2007 file
$helper->write($spreadsheet, __FILE__, ['Xlsx'], true);
