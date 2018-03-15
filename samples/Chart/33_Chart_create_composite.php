<?php

use PhpOffice\PhpSpreadsheet\Chart\Chart;
use PhpOffice\PhpSpreadsheet\Chart\DataSeries;
use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
use PhpOffice\PhpSpreadsheet\Chart\Legend;
use PhpOffice\PhpSpreadsheet\Chart\PlotArea;
use PhpOffice\PhpSpreadsheet\Chart\Title;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

require __DIR__ . '/../Header.php';

$spreadsheet = new Spreadsheet();
$worksheet = $spreadsheet->getActiveSheet();
$worksheet->fromArray(
    [
            ['', 'Rainfall (mm)', 'Temperature (°F)', 'Humidity (%)'],
            ['Jan', 78, 52, 61],
            ['Feb', 64, 54, 62],
            ['Mar', 62, 57, 63],
            ['Apr', 21, 62, 59],
            ['May', 11, 75, 60],
            ['Jun', 1, 75, 57],
            ['Jul', 1, 79, 56],
            ['Aug', 1, 79, 59],
            ['Sep', 10, 75, 60],
            ['Oct', 40, 68, 63],
            ['Nov', 69, 62, 64],
            ['Dec', 89, 57, 66],
        ]
);

//	Set the Labels for each data series we want to plot
//		Datatype
//		Cell reference for data
//		Format Code
//		Number of datapoints in series
//		Data values
//		Data Marker
$dataSeriesLabels1 = [
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$B$1', null, 1), //	Temperature
];
$dataSeriesLabels2 = [
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$C$1', null, 1), //	Rainfall
];
$dataSeriesLabels3 = [
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$D$1', null, 1), //	Humidity
];

//	Set the X-Axis Labels
//		Datatype
//		Cell reference for data
//		Format Code
//		Number of datapoints in series
//		Data values
//		Data Marker
$xAxisTickValues = [
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$A$2:$A$13', null, 12), //	Jan to Dec
];

//	Set the Data values for each data series we want to plot
//		Datatype
//		Cell reference for data
//		Format Code
//		Number of datapoints in series
//		Data values
//		Data Marker
$dataSeriesValues1 = [
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$B$2:$B$13', null, 12),
];

//	Build the dataseries
$series1 = new DataSeries(
    DataSeries::TYPE_BARCHART, // plotType
    DataSeries::GROUPING_CLUSTERED, // plotGrouping
    range(0, count($dataSeriesValues1) - 1), // plotOrder
    $dataSeriesLabels1, // plotLabel
    $xAxisTickValues, // plotCategory
    $dataSeriesValues1        // plotValues
);
//	Set additional dataseries parameters
//		Make it a vertical column rather than a horizontal bar graph
$series1->setPlotDirection(DataSeries::DIRECTION_COL);

//	Set the Data values for each data series we want to plot
//		Datatype
//		Cell reference for data
//		Format Code
//		Number of datapoints in series
//		Data values
//		Data Marker
$dataSeriesValues2 = [
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$C$2:$C$13', null, 12),
];

//	Build the dataseries
$series2 = new DataSeries(
    DataSeries::TYPE_LINECHART, // plotType
    DataSeries::GROUPING_STANDARD, // plotGrouping
    range(0, count($dataSeriesValues2) - 1), // plotOrder
    $dataSeriesLabels2, // plotLabel
    [], // plotCategory
    $dataSeriesValues2        // plotValues
);

//	Set the Data values for each data series we want to plot
//		Datatype
//		Cell reference for data
//		Format Code
//		Number of datapoints in series
//		Data values
//		Data Marker
$dataSeriesValues3 = [
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$D$2:$D$13', null, 12),
];

//	Build the dataseries
$series3 = new DataSeries(
    DataSeries::TYPE_AREACHART, // plotType
    DataSeries::GROUPING_STANDARD, // plotGrouping
    range(0, count($dataSeriesValues2) - 1), // plotOrder
    $dataSeriesLabels3, // plotLabel
    [], // plotCategory
    $dataSeriesValues3        // plotValues
);

//	Set the series in the plot area
$plotArea = new PlotArea(null, [$series1, $series2, $series3]);
//	Set the chart legend
$legend = new Legend(Legend::POSITION_RIGHT, null, false);

$title = new Title('Average Weather Chart for Crete');

//	Create the chart
$chart = new Chart(
    'chart1', // name
    $title, // title
    $legend, // legend
    $plotArea, // plotArea
    true, // plotVisibleOnly
    0, // displayBlanksAs
    null, // xAxisLabel
    null   // yAxisLabel
);

//	Set the position where the chart should appear in the worksheet
$chart->setTopLeftPosition('F2');
$chart->setBottomRightPosition('O16');

//	Add the chart to the worksheet
$worksheet->addChart($chart);

// Save Excel 2007 file
$filename = $helper->getFilename(__FILE__);
$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
$writer->setIncludeCharts(true);
$callStartTime = microtime(true);
$writer->save($filename);
$helper->logWrite($writer, $filename, $callStartTime);
