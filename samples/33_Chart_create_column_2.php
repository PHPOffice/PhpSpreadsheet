<?php

/** PhpSpreadsheet */
require __DIR__ . '/Header.php';

$spreadsheet = new \PhpSpreadsheet\Spreadsheet();
$worksheet = $spreadsheet->getActiveSheet();
$worksheet->fromArray(
    [
            ['', '', 'Budget', 'Forecast', 'Actual'],
            ['2010', 'Q1', 47, 44, 43],
            ['', 'Q2', 56, 53, 50],
            ['', 'Q3', 52, 46, 45],
            ['', 'Q4', 45, 40, 40],
            ['2011', 'Q1', 51, 42, 46],
            ['', 'Q2', 53, 58, 56],
            ['', 'Q3', 64, 66, 69],
            ['', 'Q4', 54, 55, 56],
            ['2012', 'Q1', 49, 52, 58],
            ['', 'Q2', 68, 73, 86],
            ['', 'Q3', 72, 78, 0],
            ['', 'Q4', 50, 60, 0],
        ]
);

//	Set the Labels for each data series we want to plot
//		Datatype
//		Cell reference for data
//		Format Code
//		Number of datapoints in series
//		Data values
//		Data Marker
$dataSeriesLabels = [
    new \PhpSpreadsheet\Chart\DataSeriesValues('String', 'Worksheet!$C$1', null, 1), //	'Budget'
    new \PhpSpreadsheet\Chart\DataSeriesValues('String', 'Worksheet!$D$1', null, 1), //	'Forecast'
    new \PhpSpreadsheet\Chart\DataSeriesValues('String', 'Worksheet!$E$1', null, 1), //	'Actual'
];
//	Set the X-Axis Labels
//		Datatype
//		Cell reference for data
//		Format Code
//		Number of datapoints in series
//		Data values
//		Data Marker
$xAxisTickValues = [
    new \PhpSpreadsheet\Chart\DataSeriesValues('String', 'Worksheet!$A$2:$B$13', null, 12), //	Q1 to Q4 for 2010 to 2012
];
//	Set the Data values for each data series we want to plot
//		Datatype
//		Cell reference for data
//		Format Code
//		Number of datapoints in series
//		Data values
//		Data Marker
$dataSeriesValues = [
    new \PhpSpreadsheet\Chart\DataSeriesValues('Number', 'Worksheet!$C$2:$C$13', null, 12),
    new \PhpSpreadsheet\Chart\DataSeriesValues('Number', 'Worksheet!$D$2:$D$13', null, 12),
    new \PhpSpreadsheet\Chart\DataSeriesValues('Number', 'Worksheet!$E$2:$E$13', null, 12),
];

//	Build the dataseries
$series = new \PhpSpreadsheet\Chart\DataSeries(
    \PhpSpreadsheet\Chart\DataSeries::TYPE_BARCHART, // plotType
    \PhpSpreadsheet\Chart\DataSeries::GROUPING_CLUSTERED, // plotGrouping
    range(0, count($dataSeriesValues) - 1), // plotOrder
    $dataSeriesLabels, // plotLabel
    $xAxisTickValues, // plotCategory
    $dataSeriesValues        // plotValues
);
//	Set additional dataseries parameters
//		Make it a vertical column rather than a horizontal bar graph
$series->setPlotDirection(\PhpSpreadsheet\Chart\DataSeries::DIRECTION_COL);

//	Set the series in the plot area
$plotArea = new \PhpSpreadsheet\Chart\PlotArea(null, [$series]);
//	Set the chart legend
$legend = new \PhpSpreadsheet\Chart\Legend(\PhpSpreadsheet\Chart\Legend::POSITION_BOTTOM, null, false);

$title = new \PhpSpreadsheet\Chart\Title('Test Grouped Column Chart');
$xAxisLabel = new \PhpSpreadsheet\Chart\Title('Financial Period');
$yAxisLabel = new \PhpSpreadsheet\Chart\Title('Value ($k)');

//	Create the chart
$chart = new \PhpSpreadsheet\Chart(
    'chart1', // name
    $title, // title
    $legend, // legend
    $plotArea, // plotArea
    true, // plotVisibleOnly
    0, // displayBlanksAs
    $xAxisLabel, // xAxisLabel
    $yAxisLabel  // yAxisLabel
);

//	Set the position where the chart should appear in the worksheet
$chart->setTopLeftPosition('G2');
$chart->setBottomRightPosition('P20');

//	Add the chart to the worksheet
$worksheet->addChart($chart);

// Save Excel 2007 file
$filename = $helper->getFilename(__FILE__);
$writer = \PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Excel2007');
$writer->setIncludeCharts(true);
$callStartTime = microtime(true);
$writer->save($filename);
$helper->logWrite($writer, $filename, $callStartTime);
