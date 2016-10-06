<?php


require __DIR__ . '/Header.php';

$spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
$worksheet = $spreadsheet->getActiveSheet();
$worksheet->fromArray(
    [
            ['', 2010, 2011, 2012],
            ['Q1', 12, 15, 21],
            ['Q2', 56, 73, 86],
            ['Q3', 52, 61, 69],
            ['Q4', 30, 32, 0],
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
    new \PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues('String', 'Worksheet!$B$1', null, 1), //	2010
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
    new \PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues('String', 'Worksheet!$A$2:$A$5', null, 4), //	Q1 to Q4
];
//	Set the Data values for each data series we want to plot
//		Datatype
//		Cell reference for data
//		Format Code
//		Number of datapoints in series
//		Data values
//		Data Marker
$dataSeriesValues = [
    new \PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues('Number', 'Worksheet!$B$2:$B$5', null, 4),
    new \PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues('Number', 'Worksheet!$C$2:$C$5', null, 4),
    new \PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues('Number', 'Worksheet!$D$2:$D$5', null, 4),
];

//	Build the dataseries
$series = new \PhpOffice\PhpSpreadsheet\Chart\DataSeries(
    \PhpOffice\PhpSpreadsheet\Chart\DataSeries::TYPE_BARCHART, // plotType
    \PhpOffice\PhpSpreadsheet\Chart\DataSeries::GROUPING_STACKED, // plotGrouping
    range(0, count($dataSeriesValues) - 1), // plotOrder
    $dataSeriesLabels, // plotLabel
    $xAxisTickValues, // plotCategory
    $dataSeriesValues        // plotValues
);
//	Set additional dataseries parameters
//		Make it a horizontal bar rather than a vertical column graph
$series->setPlotDirection(\PhpOffice\PhpSpreadsheet\Chart\DataSeries::DIRECTION_BAR);

//	Set the series in the plot area
$plotArea = new \PhpOffice\PhpSpreadsheet\Chart\PlotArea(null, [$series]);
//	Set the chart legend
$legend = new \PhpOffice\PhpSpreadsheet\Chart\Legend(\PhpOffice\PhpSpreadsheet\Chart\Legend::POSITION_RIGHT, null, false);

$title = new \PhpOffice\PhpSpreadsheet\Chart\Title('Test Chart');
$yAxisLabel = new \PhpOffice\PhpSpreadsheet\Chart\Title('Value ($k)');

//	Create the chart
$chart = new \PhpOffice\PhpSpreadsheet\Chart(
    'chart1', // name
    $title, // title
    $legend, // legend
    $plotArea, // plotArea
    true, // plotVisibleOnly
    0, // displayBlanksAs
    null, // xAxisLabel
    $yAxisLabel  // yAxisLabel
);

//	Set the position where the chart should appear in the worksheet
$chart->setTopLeftPosition('A7');
$chart->setBottomRightPosition('H20');

//	Add the chart to the worksheet
$worksheet->addChart($chart);

// Save Excel 2007 file
$filename = $helper->getFilename(__FILE__);
$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
$writer->setIncludeCharts(true);
$callStartTime = microtime(true);
$writer->save($filename);
$helper->logWrite($writer, $filename, $callStartTime);
