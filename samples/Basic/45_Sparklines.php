<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Sparkline\Sparkline;
use PhpOffice\PhpSpreadsheet\Worksheet\Sparkline\SparklineGroup;
use PhpOffice\PhpSpreadsheet\Worksheet\Sparkline\SparklineType;

require __DIR__ . '/../Header.php';
/** @var PhpOffice\PhpSpreadsheet\Helper\Sample $helper */
$spreadsheet = new Spreadsheet();
$worksheet = $spreadsheet->getActiveSheet();
$worksheet->setTitle('Sparklines');

$helper->log('Add some data to plot');
$worksheet->fromArray(
    [
        ['Region', 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Trend'],
        ['North', 10, 12, 9, 15, 18],
        ['South', 20, 18, 22, 17, 14],
        ['East', -3, 4, -1, 5, -2],
    ],
    null,
    'A1'
);

$helper->log('Add a line sparkline');
$worksheet->addSparkline(new Sparkline('G2', 'Sparklines!B2:F2'));

$helper->log('Add a column sparkline group with markers');
$columnGroup = new SparklineGroup();
$columnGroup->setType(SparklineType::Column)
    ->setDisplayHigh(true)
    ->setDisplayLow(true)
    ->setColorSeries('FF00B050')
    ->createSparkline('G3', 'Sparklines!B3:F3');
$worksheet->addSparklineGroup($columnGroup);

$helper->log('Add a win/loss sparkline');
$winLossGroup = new SparklineGroup();
$winLossGroup->setType(SparklineType::WinLoss)
    ->setDisplayNegative(true)
    ->createSparkline('G4', 'Sparklines!B4:F4');
$worksheet->addSparklineGroup($winLossGroup);

// Save
$helper->write($spreadsheet, __FILE__, ['Xlsx']);
