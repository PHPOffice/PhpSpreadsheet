<?php

require __DIR__ . '/Header.php';

// Create temporary file that will be read
$sampleSpreadsheet = require __DIR__ . '/templates/sampleSpreadsheet.php';
$filename = $helper->getTemporaryFilename();
$writer = new \PhpSpreadsheet\Writer\Excel2007($sampleSpreadsheet);
$writer->save($filename);

$inputFileType = \PhpSpreadsheet\IOFactory::identify($filename);
$reader = \PhpSpreadsheet\IOFactory::createReader($inputFileType);
$sheetList = $reader->listWorksheetNames($filename);
$sheetInfo = $reader->listWorksheetInfo($filename);

$helper->log('File Type:');
var_dump($inputFileType);

$helper->log('Worksheet Names:');
var_dump($sheetList);

$helper->log('Worksheet Names:');
var_dump($sheetInfo);
