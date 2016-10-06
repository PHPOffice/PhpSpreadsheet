<?php


require __DIR__ . '/Header.php';

$spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
$worksheet = $spreadsheet->getActiveSheet();
$worksheet->fromArray(
    [
            ['', 2010, 2011, 2012],
            ['Jan', 47, 45, 71],
            ['Feb', 56, 73, 86],
            ['Mar', 52, 61, 69],
            ['Apr', 40, 52, 60],
            ['May', 42, 55, 71],
            ['Jun', 58, 63, 76],
            ['Jul', 53, 61, 89],
            ['Aug', 46, 69, 85],
            ['Sep', 62, 75, 81],
            ['Oct', 51, 70, 96],
            ['Nov', 55, 66, 89],
            ['Dec', 68, 62, 0],
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
    new \PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues('String', 'Worksheet!$C$1', null, 1), //	2011
    new \PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues('String', 'Worksheet!$D$1', null, 1), //	2012
];
//	Set the X-Axis Labels
//		Datatype
//		Cell reference for data
//		Format Code
//		Number of datapoints in series
//		Data values
//		Data Marker
$xAxisTickValues = [
    new \PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues('String', 'Worksheet!$A$2:$A$13', null, 12), //	Jan to Dec
    new \PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues('String', 'Worksheet!$A$2:$A$13', null, 12), //	Jan to Dec
];
//	Set the Data values for each data series we want to plot
//		Datatype
//		Cell reference for data
//		Format Code
//		Number of datapoints in series
//		Data values
//		Data Marker
$dataSeriesValues = [
    new \PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues('Number', 'Worksheet!$C$2:$C$13', null, 12),
    new \PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues('Number', 'Worksheet!$D$2:$D$13', null, 12),
];

//	Build the dataseries
$series = new \PhpOffice\PhpSpreadsheet\Chart\DataSeries(
    \PhpOffice\PhpSpreadsheet\Chart\DataSeries::TYPE_RADARCHART, // plotType
    null, // plotGrouping (Radar charts don't have any grouping)
    range(0, count($dataSeriesValues) - 1), // plotOrder
    $dataSeriesLabels, // plotLabel
    $xAxisTickValues, // plotCategory
    $dataSeriesValues, // plotValues
    null, // plotDirection
    null, // smooth line
    \PhpOffice\PhpSpreadsheet\Chart\DataSeries::STYLE_MARKER  // plotStyle
);

//	Set up a layout object for the Pie chart
$layout = new \PhpOffice\PhpSpreadsheet\Chart\Layout();

//	Set the series in the plot area
$plotArea = new \PhpOffice\PhpSpreadsheet\Chart\PlotArea($layout, [$series]);
//	Set the chart legend
$legend = new \PhpOffice\PhpSpreadsheet\Chart\Legend(\PhpOffice\PhpSpreadsheet\Chart\Legend::POSITION_RIGHT, null, false);

$title = new \PhpOffice\PhpSpreadsheet\Chart\Title('Test Radar Chart');

//	Create the chart
$chart = new \PhpOffice\PhpSpreadsheet\Chart(
    'chart1', // name
    $title, // title
    $legend, // legend
    $plotArea, // plotArea
    true, // plotVisibleOnly
    0, // displayBlanksAs
    null, // xAxisLabel
    null   // yAxisLabel		- Radar charts don't have a Y-Axis
);

//	Set the position where the chart should appear in the worksheet
$chart->setTopLeftPosition('F2');
$chart->setBottomRightPosition('M15');

//	Add the chart to the worksheet
$worksheet->addChart($chart);

// Save Excel 2007 file
$filename = $helper->getFilename(__FILE__);
$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
$writer->setIncludeCharts(true);
$callStartTime = microtime(true);
$writer->save($filename);
$helper->logWrite($writer, $filename, $callStartTime);
