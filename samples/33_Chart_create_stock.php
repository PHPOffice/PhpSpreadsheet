<?php

use PhpOffice\PhpSpreadsheet\Chart;
use PhpOffice\PhpSpreadsheet\Chart\DataSeries;
use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
use PhpOffice\PhpSpreadsheet\Chart\Legend;
use PhpOffice\PhpSpreadsheet\Chart\PlotArea;
use PhpOffice\PhpSpreadsheet\Chart\Title;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

require __DIR__ . '/Header.php';

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

//	Set the Labels for each data series we want to plot
//		Datatype
//		Cell reference for data
//		Format Code
//		Number of datapoints in series
//		Data values
//		Data Marker
$dataSeriesLabels = [
    new DataSeriesValues('String', 'Worksheet!$B$1', null, 1), //Max / Open
    new DataSeriesValues('String', 'Worksheet!$C$1', null, 1), //Min / Close
    new DataSeriesValues('String', 'Worksheet!$D$1', null, 1), //Min Threshold / Min
    new DataSeriesValues('String', 'Worksheet!$E$1', null, 1), //Max Threshold / Max
];
//	Set the X-Axis Labels
//		Datatype
//		Cell reference for data
//		Format Code
//		Number of datapoints in series
//		Data values
//		Data Marker
$xAxisTickValues = [
    new DataSeriesValues('String', 'Worksheet!$A$2:$A$6', null, 5), //	Counts
];
//	Set the Data values for each data series we want to plot
//		Datatype
//		Cell reference for data
//		Format Code
//		Number of datapoints in series
//		Data values
//		Data Marker
$dataSeriesValues = [
    new DataSeriesValues('Number', 'Worksheet!$B$2:$B$6', null, 5),
    new DataSeriesValues('Number', 'Worksheet!$C$2:$C$6', null, 5),
    new DataSeriesValues('Number', 'Worksheet!$D$2:$D$6', null, 5),
    new DataSeriesValues('Number', 'Worksheet!$E$2:$E$6', null, 5),
];

//	Build the dataseries
$series = new DataSeries(
    DataSeries::TYPE_STOCKCHART, // plotType
    null, // plotGrouping - if we set this to not null, then xlsx throws error
    range(0, count($dataSeriesValues) - 1), // plotOrder
    $dataSeriesLabels, // plotLabel
    $xAxisTickValues, // plotCategory
    $dataSeriesValues       // plotValues
);

//	Set the series in the plot area
$plotArea = new PlotArea(null, [$series]);
//	Set the chart legend
$legend = new Legend(Legend::POSITION_RIGHT, null, false);

$title = new Title('Test Stock Chart');
$xAxisLabel = new Title('Counts');
$yAxisLabel = new Title('Values');

//	Create the chart
$chart = new Chart(
    'stock-chart', // name
    $title, // title
    $legend, // legend
    $plotArea, // plotArea
    true, // plotVisibleOnly
    0, // displayBlanksAs
    $xAxisLabel, // xAxisLabel
    $yAxisLabel  // yAxisLabel
);

//	Set the position where the chart should appear in the worksheet
$chart->setTopLeftPosition('A7');
$chart->setBottomRightPosition('H20');

//	Add the chart to the worksheet
$worksheet->addChart($chart);

// Save Excel 2007 file
$filename = $helper->getFilename(__FILE__);
$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
$writer->setIncludeCharts(true);
$callStartTime = microtime(true);
$writer->save($filename);
$helper->logWrite($writer, $filename, $callStartTime);
