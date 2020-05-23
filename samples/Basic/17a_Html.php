<?php

use PhpOffice\PhpSpreadsheet\Writer\Html;

require __DIR__ . '/../Header.php';
$spreadsheet = require __DIR__ . '/../templates/sampleSpreadsheet.php';

$filename = $helper->getFilename(__FILE__, 'html');
$writer = new Html($spreadsheet);

$callStartTime = microtime(true);
$writer->setEmbedImages(true);
$writer->save($filename);
$helper->logWrite($writer, $filename, $callStartTime);
