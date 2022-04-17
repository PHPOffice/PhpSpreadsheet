<?php

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as Reader;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as Writer;

require __DIR__ . '/../Header.php';

// Create temporary file that will be read
$sampleSpreadsheet = require __DIR__ . '/../templates/sampleSpreadsheet.php';
$filename = $helper->getTemporaryFilename();
$writer = new Writer($sampleSpreadsheet);
$writer->save($filename);

$inputFileType = IOFactory::identify($filename);
$reader = new Reader();
$sheetList = $reader->listWorksheetNames($filename);
$sheetInfo = $reader->listWorksheetInfo($filename);

$helper->log('File Type:');
var_dump($inputFileType);

$helper->log('Worksheet Names:');
var_dump($sheetList);

$helper->log('Worksheet Names:');
var_dump($sheetInfo);

unlink($filename);
