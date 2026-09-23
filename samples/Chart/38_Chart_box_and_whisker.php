<?php

use PhpOffice\PhpSpreadsheet\Chart\BoxWhisker;
use PhpOffice\PhpSpreadsheet\Chart\BoxWhiskerSeries;
use PhpOffice\PhpSpreadsheet\Chart\Chart;
use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
use PhpOffice\PhpSpreadsheet\Chart\Title;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

require __DIR__ . '/../Header.php';
/** @var PhpOffice\PhpSpreadsheet\Helper\Sample $helper */
$spreadsheet = new Spreadsheet();
$worksheet = $spreadsheet->getActiveSheet();

$worksheet->fromArray([
    ['Group A', 'Group B', 'Group C'],
    [12, 8, 15],
    [15, 11, 19],
    [18, 14, 23],
    [21, 17, 27],
    [25, 22, 31],
], null, 'A1');

$series = [
    new BoxWhiskerSeries(
        new DataSeriesValues('String', 'Worksheet!$A$1', null, 1),
        new DataSeriesValues('Number', 'Worksheet!$A$2:$A$6', null, 5)
    ),
    new BoxWhiskerSeries(
        new DataSeriesValues('String', 'Worksheet!$B$1', null, 1),
        new DataSeriesValues('Number', 'Worksheet!$B$2:$B$6', null, 5)
    ),
    new BoxWhiskerSeries(
        new DataSeriesValues('String', 'Worksheet!$C$1', null, 1),
        new DataSeriesValues('Number', 'Worksheet!$C$2:$C$6', null, 5)
    ),
];

$boxWhisker = new BoxWhisker($series);
$boxWhisker->setShowMeanLine(true)
    ->setShowMeanMarker(true)
    ->setShowInnerPoints(true)
    ->setShowOutliers(true)
    ->setQuartileMethod(BoxWhisker::QUARTILE_METHOD_EXCLUSIVE);

$chart = new Chart(
    'boxWhisker',
    new Title('Box & Whisker Chart'),
    null,
    null
);

$chart->setChartEx($boxWhisker);
$chart->setTopLeftPosition('E2');
$chart->setBottomRightPosition('M20');

$worksheet->addChart($chart);

$callStartTime = microtime(true);
$helper->write($spreadsheet, __FILE__, ['Xlsx'], true);

$spreadsheet->disconnectWorksheets();
unset($spreadsheet);
